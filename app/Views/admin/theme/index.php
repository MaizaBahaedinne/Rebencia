<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-palette"></i> <?= esc($page_title) ?>
        </h1>
        <button type="button" onclick="resetTheme()" class="btn btn-warning">
            <i class="fas fa-undo"></i> Réinitialiser
        </button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6><i class="fas fa-exclamation-triangle"></i> Erreurs :</h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/theme/update') ?>" method="post" id="themeForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Panneau de configuration -->
            <div class="col-lg-8">
                <!-- Couleurs -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-palette"></i> Palette de Couleurs
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Couleur primaire -->
                            <div class="col-md-4 mb-3">
                                <label for="primary_color" class="form-label">Couleur Primaire</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="primary_color" name="primary_color" 
                                           value="<?= old('primary_color', $theme['primary_color']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('primary_color', $theme['primary_color']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Utilisée pour les éléments principaux</small>
                            </div>

                            <!-- Couleur secondaire -->
                            <div class="col-md-4 mb-3">
                                <label for="secondary_color" class="form-label">Couleur Secondaire</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="secondary_color" name="secondary_color" 
                                           value="<?= old('secondary_color', $theme['secondary_color']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('secondary_color', $theme['secondary_color']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Complémentaire à la primaire</small>
                            </div>

                            <!-- Couleur d'accent -->
                            <div class="col-md-4 mb-3">
                                <label for="accent_color" class="form-label">Couleur d'Accent</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="accent_color" name="accent_color" 
                                           value="<?= old('accent_color', $theme['accent_color']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('accent_color', $theme['accent_color']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Pour les éléments importants</small>
                            </div>

                            <!-- Texte sombre -->
                            <div class="col-md-4 mb-3">
                                <label for="text_dark" class="form-label">Texte Sombre</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="text_dark" name="text_dark" 
                                           value="<?= old('text_dark', $theme['text_dark']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('text_dark', $theme['text_dark']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Texte principal</small>
                            </div>

                            <!-- Texte clair -->
                            <div class="col-md-4 mb-3">
                                <label for="text_light" class="form-label">Texte Clair</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="text_light" name="text_light" 
                                           value="<?= old('text_light', $theme['text_light']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('text_light', $theme['text_light']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Texte sur fonds sombres</small>
                            </div>

                            <!-- Fond clair -->
                            <div class="col-md-4 mb-3">
                                <label for="background_light" class="form-label">Fond Clair</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="background_light" name="background_light" 
                                           value="<?= old('background_light', $theme['background_light']) ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('background_light', $theme['background_light']) ?>"
                                           readonly>
                                </div>
                                <small class="text-muted">Arrière-plan général</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typographie -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-font"></i> Typographie
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Police primaire -->
                            <div class="col-md-4 mb-3">
                                <label for="font_family_primary" class="form-label">Police Primaire</label>
                                <select class="form-select" id="font_family_primary" name="font_family_primary" onchange="updatePreview()">
                                    <option value="Poppins" <?= $theme['font_family_primary'] === 'Poppins' ? 'selected' : '' ?>>Poppins</option>
                                    <option value="Roboto" <?= $theme['font_family_primary'] === 'Roboto' ? 'selected' : '' ?>>Roboto</option>
                                    <option value="Open Sans" <?= $theme['font_family_primary'] === 'Open Sans' ? 'selected' : '' ?>>Open Sans</option>
                                    <option value="Montserrat" <?= $theme['font_family_primary'] === 'Montserrat' ? 'selected' : '' ?>>Montserrat</option>
                                    <option value="Lato" <?= $theme['font_family_primary'] === 'Lato' ? 'selected' : '' ?>>Lato</option>
                                    <option value="Raleway" <?= $theme['font_family_primary'] === 'Raleway' ? 'selected' : '' ?>>Raleway</option>
                                    <option value="Inter" <?= $theme['font_family_primary'] === 'Inter' ? 'selected' : '' ?>>Inter</option>
                                    <option value="Nunito" <?= $theme['font_family_primary'] === 'Nunito' ? 'selected' : '' ?>>Nunito</option>
                                </select>
                                <small class="text-muted">Pour les titres</small>
                            </div>

                            <!-- Police secondaire -->
                            <div class="col-md-4 mb-3">
                                <label for="font_family_secondary" class="form-label">Police Secondaire</label>
                                <select class="form-select" id="font_family_secondary" name="font_family_secondary" onchange="updatePreview()">
                                    <option value="Roboto" <?= $theme['font_family_secondary'] === 'Roboto' ? 'selected' : '' ?>>Roboto</option>
                                    <option value="Poppins" <?= $theme['font_family_secondary'] === 'Poppins' ? 'selected' : '' ?>>Poppins</option>
                                    <option value="Open Sans" <?= $theme['font_family_secondary'] === 'Open Sans' ? 'selected' : '' ?>>Open Sans</option>
                                    <option value="Lato" <?= $theme['font_family_secondary'] === 'Lato' ? 'selected' : '' ?>>Lato</option>
                                    <option value="Raleway" <?= $theme['font_family_secondary'] === 'Raleway' ? 'selected' : '' ?>>Raleway</option>
                                    <option value="Inter" <?= $theme['font_family_secondary'] === 'Inter' ? 'selected' : '' ?>>Inter</option>
                                    <option value="Nunito" <?= $theme['font_family_secondary'] === 'Nunito' ? 'selected' : '' ?>>Nunito</option>
                                    <option value="Merriweather" <?= $theme['font_family_secondary'] === 'Merriweather' ? 'selected' : '' ?>>Merriweather</option>
                                </select>
                                <small class="text-muted">Pour le contenu</small>
                            </div>

                            <!-- Taille de base -->
                            <div class="col-md-4 mb-3">
                                <label for="font_size_base" class="form-label">Taille de Base</label>
                                <select class="form-select" id="font_size_base" name="font_size_base" onchange="updatePreview()">
                                    <option value="14px" <?= $theme['font_size_base'] === '14px' ? 'selected' : '' ?>>14px - Petit</option>
                                    <option value="15px" <?= $theme['font_size_base'] === '15px' ? 'selected' : '' ?>>15px</option>
                                    <option value="16px" <?= $theme['font_size_base'] === '16px' ? 'selected' : '' ?>>16px - Standard</option>
                                    <option value="17px" <?= $theme['font_size_base'] === '17px' ? 'selected' : '' ?>>17px</option>
                                    <option value="18px" <?= $theme['font_size_base'] === '18px' ? 'selected' : '' ?>>18px - Grand</option>
                                </select>
                                <small class="text-muted">Taille du texte principal</small>
                            </div>

                            <!-- Rayon de bordure -->
                            <div class="col-md-4 mb-3">
                                <label for="border_radius" class="form-label">Rayon de Bordure</label>
                                <select class="form-select" id="border_radius" name="border_radius" onchange="updatePreview()">
                                    <option value="0px" <?= $theme['border_radius'] === '0px' ? 'selected' : '' ?>>0px - Carré</option>
                                    <option value="4px" <?= $theme['border_radius'] === '4px' ? 'selected' : '' ?>>4px</option>
                                    <option value="8px" <?= $theme['border_radius'] === '8px' ? 'selected' : '' ?>>8px - Standard</option>
                                    <option value="12px" <?= $theme['border_radius'] === '12px' ? 'selected' : '' ?>>12px</option>
                                    <option value="16px" <?= $theme['border_radius'] === '16px' ? 'selected' : '' ?>>16px - Arrondi</option>
                                </select>
                                <small class="text-muted">Arrondi des coins</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design des Boutons -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-hand-pointer"></i> Design des Boutons
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Couleur fond bouton -->
                            <div class="col-md-3 mb-3">
                                <label for="button_bg_color" class="form-label">Couleur Fond</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_bg_color" name="button_bg_color" 
                                           value="<?= old('button_bg_color', $theme['button_bg_color'] ?? '#667eea') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_bg_color', $theme['button_bg_color'] ?? '#667eea') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur texte bouton -->
                            <div class="col-md-3 mb-3">
                                <label for="button_text_color" class="form-label">Couleur Texte</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_text_color" name="button_text_color" 
                                           value="<?= old('button_text_color', $theme['button_text_color'] ?? '#ffffff') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_text_color', $theme['button_text_color'] ?? '#ffffff') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur fond hover -->
                            <div class="col-md-3 mb-3">
                                <label for="button_hover_bg_color" class="form-label">Fond Survol</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_hover_bg_color" name="button_hover_bg_color" 
                                           value="<?= old('button_hover_bg_color', $theme['button_hover_bg_color'] ?? '#764ba2') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_hover_bg_color', $theme['button_hover_bg_color'] ?? '#764ba2') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur texte hover -->
                            <div class="col-md-3 mb-3">
                                <label for="button_hover_text_color" class="form-label">Texte Survol</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_hover_text_color" name="button_hover_text_color" 
                                           value="<?= old('button_hover_text_color', $theme['button_hover_text_color'] ?? '#ffffff') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_hover_text_color', $theme['button_hover_text_color'] ?? '#ffffff') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Largeur bordure -->
                            <div class="col-md-3 mb-3">
                                <label for="button_border_width" class="form-label">Largeur Bordure</label>
                                <select class="form-select" id="button_border_width" name="button_border_width" onchange="updatePreview()">
                                    <option value="0px" <?= ($theme['button_border_width'] ?? '0px') === '0px' ? 'selected' : '' ?>>Aucune</option>
                                    <option value="1px" <?= ($theme['button_border_width'] ?? '0px') === '1px' ? 'selected' : '' ?>>1px - Fine</option>
                                    <option value="2px" <?= ($theme['button_border_width'] ?? '0px') === '2px' ? 'selected' : '' ?>>2px - Standard</option>
                                    <option value="3px" <?= ($theme['button_border_width'] ?? '0px') === '3px' ? 'selected' : '' ?>>3px - Épaisse</option>
                                </select>
                            </div>

                            <!-- Couleur bordure -->
                            <div class="col-md-3 mb-3">
                                <label for="button_border_color" class="form-label">Couleur Bordure</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_border_color" name="button_border_color" 
                                           value="<?= old('button_border_color', $theme['button_border_color'] ?? '#667eea') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_border_color', $theme['button_border_color'] ?? '#667eea') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Padding -->
                            <div class="col-md-3 mb-3">
                                <label for="button_padding" class="form-label">Espacement</label>
                                <select class="form-select" id="button_padding" name="button_padding" onchange="updatePreview()">
                                    <option value="8px 20px" <?= ($theme['button_padding'] ?? '12px 30px') === '8px 20px' ? 'selected' : '' ?>>Petit</option>
                                    <option value="12px 30px" <?= ($theme['button_padding'] ?? '12px 30px') === '12px 30px' ? 'selected' : '' ?>>Standard</option>
                                    <option value="16px 40px" <?= ($theme['button_padding'] ?? '12px 30px') === '16px 40px' ? 'selected' : '' ?>>Grand</option>
                                    <option value="20px 50px" <?= ($theme['button_padding'] ?? '12px 30px') === '20px 50px' ? 'selected' : '' ?>>Très Grand</option>
                                </select>
                            </div>

                            <!-- Taille police -->
                            <div class="col-md-3 mb-3">
                                <label for="button_font_size" class="form-label">Taille Police</label>
                                <select class="form-select" id="button_font_size" name="button_font_size" onchange="updatePreview()">
                                    <option value="14px" <?= ($theme['button_font_size'] ?? '16px') === '14px' ? 'selected' : '' ?>>14px - Petit</option>
                                    <option value="16px" <?= ($theme['button_font_size'] ?? '16px') === '16px' ? 'selected' : '' ?>>16px - Standard</option>
                                    <option value="18px" <?= ($theme['button_font_size'] ?? '16px') === '18px' ? 'selected' : '' ?>>18px - Grand</option>
                                    <option value="20px" <?= ($theme['button_font_size'] ?? '16px') === '20px' ? 'selected' : '' ?>>20px - Très Grand</option>
                                </select>
                            </div>

                            <!-- Poids police -->
                            <div class="col-md-3 mb-3">
                                <label for="button_font_weight" class="form-label">Poids Police</label>
                                <select class="form-select" id="button_font_weight" name="button_font_weight" onchange="updatePreview()">
                                    <option value="300" <?= ($theme['button_font_weight'] ?? '500') === '300' ? 'selected' : '' ?>>300 - Léger</option>
                                    <option value="400" <?= ($theme['button_font_weight'] ?? '500') === '400' ? 'selected' : '' ?>>400 - Normal</option>
                                    <option value="500" <?= ($theme['button_font_weight'] ?? '500') === '500' ? 'selected' : '' ?>>500 - Moyen</option>
                                    <option value="600" <?= ($theme['button_font_weight'] ?? '500') === '600' ? 'selected' : '' ?>>600 - Semi-Gras</option>
                                    <option value="700" <?= ($theme['button_font_weight'] ?? '500') === '700' ? 'selected' : '' ?>>700 - Gras</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design des Boutons Secondaires -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-hand-pointer"></i> Design des Boutons Secondaires
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Couleur fond bouton secondaire -->
                            <div class="col-md-3 mb-3">
                                <label for="button_secondary_bg_color" class="form-label">Couleur Fond</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_secondary_bg_color" name="button_secondary_bg_color" 
                                           value="<?= old('button_secondary_bg_color', $theme['button_secondary_bg_color'] ?? '#6c757d') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_secondary_bg_color', $theme['button_secondary_bg_color'] ?? '#6c757d') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur texte bouton secondaire -->
                            <div class="col-md-3 mb-3">
                                <label for="button_secondary_text_color" class="form-label">Couleur Texte</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_secondary_text_color" name="button_secondary_text_color" 
                                           value="<?= old('button_secondary_text_color', $theme['button_secondary_text_color'] ?? '#ffffff') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_secondary_text_color', $theme['button_secondary_text_color'] ?? '#ffffff') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur fond hover secondaire -->
                            <div class="col-md-3 mb-3">
                                <label for="button_secondary_hover_bg_color" class="form-label">Fond Survol</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_secondary_hover_bg_color" name="button_secondary_hover_bg_color" 
                                           value="<?= old('button_secondary_hover_bg_color', $theme['button_secondary_hover_bg_color'] ?? '#5a6268') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_secondary_hover_bg_color', $theme['button_secondary_hover_bg_color'] ?? '#5a6268') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur texte hover secondaire -->
                            <div class="col-md-3 mb-3">
                                <label for="button_secondary_hover_text_color" class="form-label">Texte Survol</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="button_secondary_hover_text_color" name="button_secondary_hover_text_color" 
                                           value="<?= old('button_secondary_hover_text_color', $theme['button_secondary_hover_text_color'] ?? '#ffffff') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('button_secondary_hover_text_color', $theme['button_secondary_hover_text_color'] ?? '#ffffff') ?>"
                                           readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Aperçu bouton secondaire -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold">Aperçu :</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary" id="preview-button-secondary">
                                    <i class="fas fa-times"></i> Bouton Secondaire
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Design des Liens -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-link"></i> Design des Liens Hypertexte
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Couleur lien -->
                            <div class="col-md-4 mb-3">
                                <label for="link_color" class="form-label">Couleur Normale</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="link_color" name="link_color" 
                                           value="<?= old('link_color', $theme['link_color'] ?? '#667eea') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('link_color', $theme['link_color'] ?? '#667eea') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Couleur lien hover -->
                            <div class="col-md-4 mb-3">
                                <label for="link_hover_color" class="form-label">Couleur Survol</label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" 
                                           id="link_hover_color" name="link_hover_color" 
                                           value="<?= old('link_hover_color', $theme['link_hover_color'] ?? '#764ba2') ?>"
                                           onchange="updatePreview()">
                                    <input type="text" class="form-control" 
                                           value="<?= old('link_hover_color', $theme['link_hover_color'] ?? '#764ba2') ?>"
                                           readonly>
                                </div>
                            </div>

                            <!-- Décoration lien -->
                            <div class="col-md-4 mb-3">
                                <label for="link_decoration" class="form-label">Soulignement</label>
                                <select class="form-select" id="link_decoration" name="link_decoration" onchange="updatePreview()">
                                    <option value="none" <?= ($theme['link_decoration'] ?? 'none') === 'none' ? 'selected' : '' ?>>Aucun</option>
                                    <option value="underline" <?= ($theme['link_decoration'] ?? 'none') === 'underline' ? 'selected' : '' ?>>Souligné</option>
                                    <option value="underline dotted" <?= ($theme['link_decoration'] ?? 'none') === 'underline dotted' ? 'selected' : '' ?>>Pointillé</option>
                                    <option value="underline dashed" <?= ($theme['link_decoration'] ?? 'none') === 'underline dashed' ? 'selected' : '' ?>>Tirets</option>
                                </select>
                            </div>
                        </div>

                        <!-- Aperçu lien -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <label class="form-label fw-bold">Aperçu :</label>
                            <div>
                                <a href="#" id="preview-link" onclick="return false;">Ceci est un lien hypertexte d'exemple</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow mb-4">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Enregistrer les Modifications
                        </button>
                    </div>
                </div>
            </div>

            <!-- Aperçu en temps réel -->
            <div class="col-lg-4">
                <div class="card shadow sticky-top" style="top: 20px;">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-eye"></i> Aperçu en Temps Réel
                        </h6>
                    </div>
                    <div class="card-body" id="previewPanel">
                        <!-- Exemple de bouton primaire -->
                        <div class="mb-3">
                            <button class="btn btn-preview-primary w-100" id="previewButton">
                                Bouton Primaire
                            </button>
                        </div>

                        <!-- Exemple de bouton secondaire -->
                        <div class="mb-3">
                            <button class="btn btn-preview-secondary w-100" id="preview-button-secondary">
                                Bouton Secondaire
                            </button>
                        </div>

                        <!-- Exemple de lien hypertexte -->
                        <div class="mb-3">
                            <a href="#" id="preview-link" class="preview-link d-block text-center">
                                <i class="fas fa-link"></i> Lien Hypertexte
                            </a>
                        </div>

                        <!-- Exemple de texte -->
                        <div class="mb-3">
                            <h4 id="previewTitle" style="font-family: var(--font-primary);">Titre Principal</h4>
                            <p id="previewText" style="font-family: var(--font-secondary);">
                                Ceci est un exemple de texte avec la police secondaire. 
                                Il vous permet de voir le rendu en temps réel de vos modifications.
                            </p>
                        </div>

                        <!-- Exemple de carte -->
                        <div class="card mb-3" id="previewCard">
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: var(--font-primary);">Carte Exemple</h5>
                                <p class="card-text" style="font-family: var(--font-secondary);">
                                    Cette carte montre comment vos modifications affectent les éléments de l'interface.
                                </p>
                                <a href="#" class="btn btn-preview-accent">Action</a>
                            </div>
                        </div>

                        <!-- Palette de couleurs -->
                        <div class="row g-2">
                            <div class="col-4">
                                <div id="colorPrimary" class="p-3 text-center text-white rounded">
                                    <small>Primaire</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div id="colorSecondary" class="p-3 text-center text-white rounded">
                                    <small>Secondaire</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div id="colorAccent" class="p-3 text-center text-white rounded">
                                    <small>Accent</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
:root {
    --theme-primary: <?= $theme['primary_color'] ?>;
    --theme-secondary: <?= $theme['secondary_color'] ?>;
    --theme-accent: <?= $theme['accent_color'] ?>;
    --theme-text-dark: <?= $theme['text_dark'] ?>;
    --theme-text-light: <?= $theme['text_light'] ?>;
    --theme-bg-light: <?= $theme['background_light'] ?>;
    --font-primary: <?= $theme['font_family_primary'] ?>, sans-serif;
    --font-secondary: <?= $theme['font_family_secondary'] ?>, sans-serif;
    --font-size: <?= $theme['font_size_base'] ?>;
    --radius: <?= $theme['border_radius'] ?>;
    
    /* Boutons primaires */
    --button-bg: <?= $theme['button_bg_color'] ?>;
    --button-text: <?= $theme['button_text_color'] ?>;
    --button-hover-bg: <?= $theme['button_hover_bg_color'] ?>;
    --button-hover-text: <?= $theme['button_hover_text_color'] ?>;
    --button-border-width: <?= $theme['button_border_width'] ?>;
    --button-border-color: <?= $theme['button_border_color'] ?>;
    --button-padding: <?= $theme['button_padding'] ?>;
    --button-font-size: <?= $theme['button_font_size'] ?>;
    --button-font-weight: <?= $theme['button_font_weight'] ?>;
    
    /* Boutons secondaires */
    --button-secondary-bg: <?= $theme['button_secondary_bg_color'] ?>;
    --button-secondary-text: <?= $theme['button_secondary_text_color'] ?>;
    --button-secondary-hover-bg: <?= $theme['button_secondary_hover_bg_color'] ?>;
    --button-secondary-hover-text: <?= $theme['button_secondary_hover_text_color'] ?>;
    
    /* Liens */
    --link-color: <?= $theme['link_color'] ?>;
    --link-hover-color: <?= $theme['link_hover_color'] ?>;
    --link-decoration: <?= $theme['link_decoration'] ?>;
}

.btn-preview-primary {
    background: var(--button-bg);
    color: var(--button-text);
    border: var(--button-border-width) solid var(--button-border-color);
    border-radius: var(--radius);
    padding: var(--button-padding);
    font-family: var(--font-primary);
    font-size: var(--button-font-size);
    font-weight: var(--button-font-weight);
    transition: all 0.3s ease;
}

.btn-preview-primary:hover {
    background: var(--button-hover-bg);
    color: var(--button-hover-text);
}

.btn-preview-secondary {
    background: var(--button-secondary-bg);
    color: var(--button-secondary-text);
    border: none;
    border-radius: var(--radius);
    padding: var(--button-padding);
    font-family: var(--font-primary);
    font-size: var(--button-font-size);
    font-weight: var(--button-font-weight);
    transition: all 0.3s ease;
}

.btn-preview-secondary:hover {
    background: var(--button-secondary-hover-bg);
    color: var(--button-secondary-hover-text);
}

.preview-link {
    color: var(--link-color);
    text-decoration: var(--link-decoration);
    font-size: var(--font-size);
    transition: all 0.3s ease;
}

.preview-link:hover {
    color: var(--link-hover-color);
}

.btn-preview-accent {
    background-color: var(--theme-accent);
    border: none;
    color: var(--theme-text-light);
    border-radius: var(--radius);
    padding: 8px 16px;
    font-family: var(--font-primary);
    font-size: var(--font-size);
}

#previewCard {
    border-radius: var(--radius);
    border-color: var(--theme-primary);
}

#previewTitle {
    color: var(--theme-primary);
    font-size: calc(var(--font-size) * 1.5);
}

#previewText {
    color: var(--theme-text-dark);
    font-size: var(--font-size);
}

#colorPrimary {
    background-color: var(--theme-primary);
}

#colorSecondary {
    background-color: var(--theme-secondary);
}

#colorAccent {
    background-color: var(--theme-accent);
}
</style>

<script>
function updatePreview() {
    const root = document.documentElement;
    
    // Mettre à jour les couleurs
    root.style.setProperty('--theme-primary', document.getElementById('primary_color').value);
    root.style.setProperty('--theme-secondary', document.getElementById('secondary_color').value);
    root.style.setProperty('--theme-accent', document.getElementById('accent_color').value);
    root.style.setProperty('--theme-text-dark', document.getElementById('text_dark').value);
    root.style.setProperty('--theme-text-light', document.getElementById('text_light').value);
    root.style.setProperty('--theme-bg-light', document.getElementById('background_light').value);
    
    // Mettre à jour les polices
    root.style.setProperty('--font-primary', document.getElementById('font_family_primary').value + ', sans-serif');
    root.style.setProperty('--font-secondary', document.getElementById('font_family_secondary').value + ', sans-serif');
    root.style.setProperty('--font-size', document.getElementById('font_size_base').value);
    root.style.setProperty('--radius', document.getElementById('border_radius').value);
    
    // Mettre à jour les variables CSS des boutons primaires
    if (document.getElementById('button_bg_color')) {
        root.style.setProperty('--button-bg', document.getElementById('button_bg_color').value);
        root.style.setProperty('--button-text', document.getElementById('button_text_color').value);
        root.style.setProperty('--button-hover-bg', document.getElementById('button_hover_bg_color').value);
        root.style.setProperty('--button-hover-text', document.getElementById('button_hover_text_color').value);
        root.style.setProperty('--button-border-width', document.getElementById('button_border_width').value);
        root.style.setProperty('--button-border-color', document.getElementById('button_border_color').value);
        root.style.setProperty('--button-padding', document.getElementById('button_padding').value);
        root.style.setProperty('--button-font-size', document.getElementById('button_font_size').value);
        root.style.setProperty('--button-font-weight', document.getElementById('button_font_weight').value);
    }
    
    // Mettre à jour les variables CSS des boutons secondaires
    if (document.getElementById('button_secondary_bg_color')) {
        root.style.setProperty('--button-secondary-bg', document.getElementById('button_secondary_bg_color').value);
        root.style.setProperty('--button-secondary-text', document.getElementById('button_secondary_text_color').value);
        root.style.setProperty('--button-secondary-hover-bg', document.getElementById('button_secondary_hover_bg_color').value);
        root.style.setProperty('--button-secondary-hover-text', document.getElementById('button_secondary_hover_text_color').value);
    }
    
    // Mettre à jour les variables CSS des liens
    if (document.getElementById('link_color')) {
        root.style.setProperty('--link-color', document.getElementById('link_color').value);
        root.style.setProperty('--link-hover-color', document.getElementById('link_hover_color').value);
        root.style.setProperty('--link-decoration', document.getElementById('link_decoration').value);
    }
    
    // Mettre à jour les champs texte des couleurs
    document.querySelectorAll('input[type="color"]').forEach(input => {
        const textInput = input.nextElementSibling;
        if (textInput && textInput.tagName === 'INPUT') {
            textInput.value = input.value;
        }
    });
}

function resetTheme() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser le thème aux valeurs par défaut ?')) {
        window.location.href = '<?= base_url('admin/theme/reset') ?>';
    }
}

// Charger les polices Google Fonts
const fonts = ['Poppins', 'Roboto', 'Open Sans', 'Montserrat', 'Lato', 'Raleway', 'Inter', 'Nunito', 'Merriweather'];
const link = document.createElement('link');
link.href = 'https://fonts.googleapis.com/css2?family=' + fonts.join(':wght@300;400;500;600;700&family=') + ':wght@300;400;500;600;700&display=swap';
link.rel = 'stylesheet';
document.head.appendChild(link);

// Initialiser l'aperçu au chargement
document.addEventListener('DOMContentLoaded', updatePreview);
</script>

<?= $this->endSection() ?>
