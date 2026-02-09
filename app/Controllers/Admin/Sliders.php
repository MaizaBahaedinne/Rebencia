<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Sliders extends BaseController
{
    protected $sliderModel;
    
    public function __construct()
    {
        $this->sliderModel = model('SliderModel');
    }
    
    public function index()
    {
        $data = [
            'title' => 'Gestion des Sliders',
            'page_title' => 'Sliders',
            'sliders' => $this->sliderModel->orderBy('display_order', 'ASC')->findAll()
        ];
        
        return view('admin/sliders/index', $data);
    }
    
    public function create()
    {
        $data = [
            'title' => 'Ajouter un Slider',
            'page_title' => 'Nouveau Slider',
            'next_order' => $this->sliderModel->getNextOrder()
        ];
        
        return view('admin/sliders/create', $data);
    }
    
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|max_length[255]',
            'image' => 'uploaded[image]|max_size[image,2048]|is_image[image]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $imageFile = $this->request->getFile('image');
        $imageName = null;
        
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/sliders', $imageName);
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'description' => $this->request->getPost('description'),
            'image' => $imageName,
            'button_text' => $this->request->getPost('button_text'),
            'button_link' => $this->request->getPost('button_link'),
            'button_text_2' => $this->request->getPost('button_text_2'),
            'button_link_2' => $this->request->getPost('button_link_2'),
            'display_order' => $this->request->getPost('display_order'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'animation_type' => $this->request->getPost('animation_type'),
            'text_position' => $this->request->getPost('text_position'),
            'overlay_opacity' => $this->request->getPost('overlay_opacity'),
        ];
        
        $this->sliderModel->insert($data);
        
        return redirect()->to('admin/sliders')->with('success', 'Slider ajouté avec succès');
    }
    
    public function edit($id)
    {
        $slider = $this->sliderModel->find($id);
        
        if (!$slider) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider non trouvé');
        }
        
        $data = [
            'title' => 'Modifier le Slider',
            'page_title' => 'Modifier le Slider',
            'slider' => $slider
        ];
        
        return view('admin/sliders/edit', $data);
    }
    
    public function update($id)
    {
        $slider = $this->sliderModel->find($id);
        
        if (!$slider) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Slider non trouvé');
        }
        
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|max_length[255]',
        ];
        
        // Only validate image if uploaded
        if ($this->request->getFile('image')->isValid()) {
            $rules['image'] = 'max_size[image,2048]|is_image[image]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'description' => $this->request->getPost('description'),
            'button_text' => $this->request->getPost('button_text'),
            'button_link' => $this->request->getPost('button_link'),
            'button_text_2' => $this->request->getPost('button_text_2'),
            'button_link_2' => $this->request->getPost('button_link_2'),
            'display_order' => $this->request->getPost('display_order'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'animation_type' => $this->request->getPost('animation_type'),
            'text_position' => $this->request->getPost('text_position'),
            'overlay_opacity' => $this->request->getPost('overlay_opacity'),
        ];
        
        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            // Delete old image
            if ($slider['image'] && file_exists(ROOTPATH . 'public/uploads/sliders/' . $slider['image'])) {
                unlink(ROOTPATH . 'public/uploads/sliders/' . $slider['image']);
            }
            
            $imageName = $imageFile->getRandomName();
            $imageFile->move(ROOTPATH . 'public/uploads/sliders', $imageName);
            $data['image'] = $imageName;
        }
        
        $this->sliderModel->update($id, $data);
        
        return redirect()->to('admin/sliders')->with('success', 'Slider modifié avec succès');
    }
    
    public function delete($id)
    {
        $slider = $this->sliderModel->find($id);
        
        if (!$slider) {
            return redirect()->to('admin/sliders')->with('error', 'Slider non trouvé');
        }
        
        // Delete image file
        if ($slider['image'] && file_exists(ROOTPATH . 'public/uploads/sliders/' . $slider['image'])) {
            unlink(ROOTPATH . 'public/uploads/sliders/' . $slider['image']);
        }
        
        $this->sliderModel->delete($id);
        
        return redirect()->to('admin/sliders')->with('success', 'Slider supprimé avec succès');
    }
    
    public function toggleStatus($id)
    {
        $slider = $this->sliderModel->find($id);
        
        if (!$slider) {
            return $this->response->setJSON(['success' => false, 'message' => 'Slider non trouvé']);
        }
        
        $this->sliderModel->update($id, ['is_active' => !$slider['is_active']]);
        
        return $this->response->setJSON(['success' => true, 'is_active' => !$slider['is_active']]);
    }
}
