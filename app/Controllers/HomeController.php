<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\LeadModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * HomeController — Site vitrine public (FR / EN / AR)
 */
class HomeController extends BaseController
{
    protected PropertyModel                         $propertyModel;
    protected LeadModel                             $leadModel;
    protected \CodeIgniter\Database\BaseConnection  $db;

    /** Langues supportées */
    private const SUPPORTED_LANGS = ['fr', 'en', 'ar'];
    private const DEFAULT_LANG    = 'fr';

    public function __construct()
    {
        $this->propertyModel = new PropertyModel();
        $this->leadModel     = new LeadModel();
        $this->db            = \Config\Database::connect();
    }

    // =========================================================
    //  HELPERS PRIVÉS
    // =========================================================

    /**
     * Valide et retourne la langue courante, puis configure CI4.
     */
    private function setLang(string $lang): string
    {
        if (!in_array($lang, self::SUPPORTED_LANGS, true)) {
            $lang = self::DEFAULT_LANG;
        }
        // Configure la langue dans CI4
        $appLang = \Config\Services::language();
        $appLang->setLocale($lang);

        return $lang;
    }

    /**
     * Données communes à toutes les vues vitrine.
     */
    private function baseData(string $lang, string $activeNav = '', string $langSwitchUri = ''): array
    {
        return [
            'lang'         => $lang,
            'currentLang'  => $lang,
            'activeNav'    => $activeNav,
            'langSwitchUri'=> $langSwitchUri,
        ];
    }

    // =========================================================
    //  REDIRECT : / → /fr/
    // =========================================================
    public function redirectHome(): RedirectResponse
    {
        // Détecter la langue du navigateur
        $accepted = $this->request->getHeaderLine('Accept-Language');
        $lang     = self::DEFAULT_LANG;
        foreach (self::SUPPORTED_LANGS as $l) {
            if (str_contains(strtolower($accepted), $l)) {
                $lang = $l;
                break;
            }
        }
        return redirect()->to(base_url($lang . '/'));
    }

    // =========================================================
    //  ACCUEIL
    // =========================================================
    public function home(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        // Biens en vedette (6 max)
        $featured = $this->propertyModel
            ->where('is_published', 1)
            ->where('featured', 1)
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('published_at', 'DESC')
            ->limit(6)
            ->findAll();

        // Stats
        $stats = [
            'properties' => $this->propertyModel->where('is_published', 1)->where('deleted_at IS NULL', null, false)->countAllResults(),
            'agents'     => $this->db->table('users')->where('role_id !=', 1)->where('deleted_at IS NULL')->countAllResults(),
            'years'      => 10,
            'sold'       => 500,
        ];

        $data = array_merge($this->baseData($lang, 'home', ''), [
            'page_title'       => lang('Vitrine.nav_home'),
            'meta_description' => lang('Vitrine.hero_subtitle'),
            'featured'         => $featured,
            'stats'            => $stats,
        ]);

        return view('vitrine/home', $data);
    }

    // =========================================================
    //  CATALOGUE
    // =========================================================
    public function properties(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        // Filtres GET
        $filters = [
            'keyword'          => $this->request->getGet('q')                ?? '',
            'type'             => $this->request->getGet('type')             ?? '',
            'transaction_type' => $this->request->getGet('transaction_type') ?? '',
            'city'             => $this->request->getGet('city')             ?? '',
            'min_price'        => (int) ($this->request->getGet('min_price') ?? 0),
            'max_price'        => (int) ($this->request->getGet('max_price') ?? 0),
            'min_surface'      => (int) ($this->request->getGet('min_surface') ?? 0),
            'sort'             => $this->request->getGet('sort')             ?? 'recent',
        ];

        $perPage = 12;
        $builder = $this->db->table('properties p')
            ->select('p.*, (SELECT file_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) AS main_image')
            ->where('p.is_published', 1)
            ->where('p.deleted_at IS NULL', null, false);

        if ($filters['keyword']) {
            $builder->groupStart()
                ->like('p.title', $filters['keyword'])
                ->orLike('p.city', $filters['keyword'])
                ->orLike('p.reference', $filters['keyword'])
                ->groupEnd();
        }
        if ($filters['type'])             $builder->where('p.type', $filters['type']);
        if ($filters['transaction_type']) $builder->where('p.transaction_type', $filters['transaction_type']);
        if ($filters['city'])             $builder->like('p.city', $filters['city']);
        if ($filters['min_price'] > 0)    $builder->where('p.price >=', $filters['min_price']);
        if ($filters['max_price'] > 0)    $builder->where('p.price <=', $filters['max_price']);
        if ($filters['min_surface'] > 0)  $builder->where('p.surface >=', $filters['min_surface']);

        match ($filters['sort']) {
            'price_asc'  => $builder->orderBy('p.price', 'ASC'),
            'price_desc' => $builder->orderBy('p.price', 'DESC'),
            'surface'    => $builder->orderBy('p.surface', 'DESC'),
            default      => $builder->orderBy('p.published_at', 'DESC'),
        };

        $total      = (clone $builder)->countAllResults(false);
        $properties = $builder->limit($perPage, (max(1, (int)($this->request->getGet('page') ?? 1)) - 1) * $perPage)->get()->getResultArray();

        $pager = \Config\Services::pager();

        $data = array_merge($this->baseData($lang, 'properties', 'properties'), [
            'page_title'  => lang('Vitrine.filter_title'),
            'properties'  => $properties,
            'filters'     => $filters,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => max(1, (int)($this->request->getGet('page') ?? 1)),
        ]);

        return view('vitrine/properties', $data);
    }

    // =========================================================
    //  DÉTAIL D'UN BIEN
    // =========================================================
    public function propertyDetail(string $lang, int $id): string
    {
        $lang = $this->setLang($lang);

        $property = $this->db->table('properties p')
            ->where('p.id', $id)
            ->where('p.is_published', 1)
            ->where('p.deleted_at IS NULL', null, false)
            ->get()->getRowArray();

        if (!$property) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Property #{$id} not found");
        }

        // Images
        $images = $this->db->table('property_images')
            ->where('property_id', $id)
            ->orderBy('is_main DESC, sort_order ASC')
            ->get()->getResultArray();

        // Agent
        $agent = null;
        if (!empty($property['agent_id'])) {
            $agent = $this->db->table('users')->where('id', $property['agent_id'])->get()->getRowArray();
        }

        // Biens similaires (même type/ville, max 3)
        $similar = $this->db->table('properties p')
            ->select('p.*, (SELECT file_path FROM property_images pi WHERE pi.property_id = p.id AND pi.is_main = 1 LIMIT 1) AS main_image')
            ->where('p.id !=', $id)
            ->where('p.is_published', 1)
            ->where('p.deleted_at IS NULL', null, false)
            ->groupStart()
                ->where('p.type', $property['type'])
                ->orWhere('p.city', $property['city'])
            ->groupEnd()
            ->orderBy('RAND()')
            ->limit(3)
            ->get()->getResultArray();

        // Incrémenter views_count
        $this->db->table('properties')->where('id', $id)->update(['views_count' => $property['views_count'] + 1]);

        $data = array_merge($this->baseData($lang, 'properties', "properties/{$id}"), [
            'page_title'       => esc($property['title']),
            'meta_description' => esc(strip_tags(substr($property['description'] ?? '', 0, 160))),
            'property'         => $property,
            'images'           => $images,
            'agent'            => $agent,
            'similar'          => $similar,
        ]);

        return view('vitrine/property_detail', $data);
    }

    // =========================================================
    //  À PROPOS
    // =========================================================
    public function about(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        // Agents affichables publiquement
        $team = $this->db->table('users u')
            ->select('u.id, u.first_name, u.last_name, u.email, u.phone, u.avatar, r.label AS role_label')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->where('u.is_active', 1)
            ->where('u.deleted_at IS NULL')
            ->orderBy('u.first_name', 'ASC')
            ->limit(12)
            ->get()->getResultArray();

        $data = array_merge($this->baseData($lang, 'about', 'about'), [
            'page_title' => lang('Vitrine.about_title'),
            'team'       => $team,
        ]);

        return view('vitrine/about', $data);
    }

    // =========================================================
    //  CONTACT → LEAD
    // =========================================================
    public function contact(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        $data = array_merge($this->baseData($lang, 'contact', 'contact'), [
            'page_title' => lang('Vitrine.contact_title'),
            'success'    => session()->getFlashdata('success'),
            'errors'     => session()->getFlashdata('errors'),
        ]);

        return view('vitrine/contact', $data);
    }

    public function contactSubmit(string $lang = 'fr'): RedirectResponse
    {
        $lang = $this->setLang($lang);

        $rules = [
            'name'    => 'required|min_length[2]|max_length[100]',
            'email'   => 'required|valid_email|max_length[150]',
            'phone'   => 'permit_empty|max_length[20]',
            'message' => 'required|min_length[10]|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name    = $this->request->getPost('name');
        $email   = $this->request->getPost('email');
        $phone   = $this->request->getPost('phone');
        $message = $this->request->getPost('message');
        $subject = $this->request->getPost('subject') ?? 'Demande de contact';

        // Enregistrer comme Lead
        $nameParts  = explode(' ', trim($name), 2);
        $firstName  = $nameParts[0];
        $lastName   = $nameParts[1] ?? '';
        $this->leadModel->insert([
            'first_name'  => $firstName,
            'last_name'   => $lastName,
            'email'       => $email,
            'phone'       => $phone,
            'source'      => 'website',
            'status'      => 'new',
            'notes'       => "[{$subject}]\n{$message}",
            'budget_min'  => 0,
            'budget_max'  => 0,
        ]);

        return redirect()->to(base_url("{$lang}/contact"))
            ->with('success', lang('Vitrine.contact_success'));
    }

    // =========================================================
    //  BLOG
    // =========================================================
    public function blog(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        $data = array_merge($this->baseData($lang, 'blog', 'blog'), [
            'page_title' => lang('Vitrine.blog_title'),
            'posts'      => [], // à alimenter quand la table blog sera créée
        ]);

        return view('vitrine/blog', $data);
    }

    // =========================================================
    //  ESTIMATION
    // =========================================================
    public function estimate(string $lang = 'fr'): string
    {
        $lang = $this->setLang($lang);

        $data = array_merge($this->baseData($lang, 'estimate', 'estimate'), [
            'page_title' => lang('Vitrine.estimate_title'),
            'success'    => session()->getFlashdata('success'),
            'errors'     => session()->getFlashdata('errors'),
        ]);

        return view('vitrine/estimate', $data);
    }

    public function estimateSubmit(string $lang = 'fr'): RedirectResponse
    {
        $lang = $this->setLang($lang);

        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|max_length[150]',
            'phone'            => 'required|max_length[20]',
            'property_type'    => 'required|max_length[50]',
            'city'             => 'required|max_length[100]',
            'surface'          => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name    = $this->request->getPost('name');
        $email   = $this->request->getPost('email');
        $phone   = $this->request->getPost('phone');
        $type    = $this->request->getPost('property_type');
        $city    = $this->request->getPost('city');
        $surface = $this->request->getPost('surface');
        $desc    = "[Estimation] Type: {$type} | Ville: {$city} | Surface: {$surface} m²";

        // Créer un lead de type estimation
        $nameParts2 = explode(' ', trim($name), 2);
        $firstName2 = $nameParts2[0];
        $lastName2  = $nameParts2[1] ?? '';
        $this->leadModel->insert([
            'first_name'       => $firstName2,
            'last_name'        => $lastName2,
            'email'            => $email,
            'phone'            => $phone,
            'source'           => 'estimation',
            'status'           => 'new',
            'notes'            => $desc,
            'desired_surface'  => (int) $surface,
            'desired_location' => $city,
            'budget_min'       => 0,
            'budget_max'       => 0,
        ]);

        return redirect()->to(base_url("{$lang}/estimate"))
            ->with('success', lang('Vitrine.estimate_success'));
    }
}
