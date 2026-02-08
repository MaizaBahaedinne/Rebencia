<!-- Étape 6: Documents et Photos -->
<div class="row g-4">
    <!-- Photos -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 mb-3">
            <i class="fas fa-images text-primary"></i> Photos du bien
        </h6>
    </div>

    <div class="col-12">
        <div class="upload-zone border border-2 border-dashed rounded p-4 text-center" id="photo-upload-zone">
            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
            <p class="mb-2">Glissez-déposez vos photos ici ou cliquez pour sélectionner</p>
            <p class="text-muted small">Formats acceptés: JPG, PNG, WEBP (Max: 5 Mo par fichier)</p>
            <input type="file" 
                   class="d-none" 
                   id="photo-input" 
                   name="photos[]" 
                   multiple 
                   accept="image/jpeg,image/png,image/webp">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photo-input').click()">
                <i class="fas fa-folder-open"></i> Parcourir
            </button>
        </div>
        
        <div id="photo-preview" class="row g-3 mt-3">
            <?php if (!empty($photos)): ?>
                <?php foreach ($photos as $photo): ?>
                    <div class="col-md-3">
                        <div class="card photo-card">
                            <img src="<?= base_url('uploads/properties/' . $photo['file_path']) ?>" class="card-img-top" alt="Photo">
                            <div class="card-body p-2">
                                <small class="text-muted d-block text-truncate"><?= esc($photo['title']) ?></small>
                                <button type="button" class="btn btn-sm btn-danger w-100 mt-1 delete-photo-btn" data-id="<?= $photo['id'] ?>">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Documents légaux et techniques -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 mb-3">
            <i class="fas fa-file-alt text-success"></i> Documents légaux et techniques
        </h6>
    </div>

    <?php
    $documentTypes = [
        'contrat' => ['label' => 'Contrat', 'icon' => 'fa-file-contract', 'color' => 'primary'],
        'titre_foncier' => ['label' => 'Titre foncier', 'icon' => 'fa-file-signature', 'color' => 'success'],
        'plan' => ['label' => 'Plans', 'icon' => 'fa-drafting-compass', 'color' => 'info'],
        'diagnostic' => ['label' => 'Diagnostics', 'icon' => 'fa-stethoscope', 'color' => 'warning'],
        'certificat' => ['label' => 'Certificats', 'icon' => 'fa-certificate', 'color' => 'danger']
    ];
    
    // Convertir les documents en tableau associatif
    $documentsMap = [];
    if (!empty($documents)) {
        foreach ($documents as $doc) {
            if (!isset($documentsMap[$doc['document_type']])) {
                $documentsMap[$doc['document_type']] = [];
            }
            $documentsMap[$doc['document_type']][] = $doc;
        }
    }
    ?>

    <?php foreach ($documentTypes as $type => $info): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-<?= $info['color'] ?> bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="fas <?= $info['icon'] ?> text-<?= $info['color'] ?> me-2"></i>
                        <?= $info['label'] ?>
                    </h6>
                </div>
                <div class="card-body">
                    <input type="file" 
                           class="form-control form-control-sm mb-2 document-input" 
                           name="documents[<?= $type ?>][]" 
                           multiple
                           accept=".pdf,.doc,.docx,.jpg,.png"
                           data-type="<?= $type ?>">
                    <small class="text-muted">PDF, DOC, DOCX, JPG, PNG (Max: 10 Mo)</small>
                    
                    <!-- Afficher les documents existants -->
                    <?php if (!empty($documentsMap[$type])): ?>
                        <div class="document-list mt-3">
                            <?php foreach ($documentsMap[$type] as $doc): ?>
                                <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                                    <div class="flex-grow-1">
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                        <small><?= esc($doc['title'] ?? $doc['file_name'] ?? 'Document') ?></small>
                                        <br>
                                        <small class="text-muted"><?= number_format($doc['file_size'] / 1024, 2) ?> Ko</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-document-btn" data-id="<?= $doc['id'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Preview zone pour nouveaux documents -->
                    <div class="document-preview-<?= $type ?> mt-2"></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Notes internes -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 mb-3">
            <i class="fas fa-sticky-note text-warning"></i> Notes internes
        </h6>
    </div>

    <div class="col-12">
        <textarea class="form-control" 
                  id="internal_notes" 
                  name="internal_notes" 
                  rows="4"
                  placeholder="Notes internes, informations complémentaires, historique..."><?= old('internal_notes', $property['internal_notes'] ?? '') ?></textarea>
        <small class="text-muted">Ces notes ne sont visibles que par les agents et administrateurs</small>
    </div>
</div>

<style>
.upload-zone {
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s;
}

.upload-zone:hover {
    background-color: #e9ecef;
    border-color: #0d6efd !important;
}

.upload-zone.dragover {
    background-color: #d1e7ff;
    border-color: #0d6efd !important;
}

.photo-card img {
    height: 150px;
    object-fit: cover;
}

.document-list {
    max-height: 300px;
    overflow-y: auto;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo-input');
    const photoZone = document.getElementById('photo-upload-zone');
    const photoPreview = document.getElementById('photo-preview');
    
    // Stockage des fichiers sélectionnés
    let photoFiles = [];
    
    // Click sur la zone de drag & drop
    photoZone.addEventListener('click', function(e) {
        if (e.target !== photoInput && !e.target.closest('button')) {
            photoInput.click();
        }
    });
    
    // Drag & Drop pour photos
    photoZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    photoZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    photoZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files);
        addPhotoFiles(files);
    });
    
    // Changement d'input fichier pour photos
    photoInput.addEventListener('change', function(e) {
        const files = Array.from(this.files);
        addPhotoFiles(files);
    });
    
    // Ajouter des fichiers photos
    function addPhotoFiles(files) {
        files.forEach(file => {
            // Vérifier le type
            if (!file.type.match('image.*')) {
                alert('Seules les images sont acceptées pour les photos');
                return;
            }
            
            // Vérifier la taille (5 Mo)
            if (file.size > 5 * 1024 * 1024) {
                alert('La taille maximale par photo est de 5 Mo');
                return;
            }
            
            photoFiles.push(file);
            
            // Prévisualisation
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="card photo-card">
                        <img src="${e.target.result}" class="card-img-top" alt="Preview">
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate">${file.name}</small>
                            <small class="text-muted d-block">${(file.size / 1024).toFixed(2)} Ko</small>
                            <button type="button" class="btn btn-sm btn-danger w-100 mt-1 remove-new-photo-btn" data-filename="${file.name}">
                                <i class="fas fa-times"></i> Retirer
                            </button>
                        </div>
                    </div>
                `;
                photoPreview.appendChild(col);
                
                // Ajouter l'événement de suppression
                col.querySelector('.remove-new-photo-btn').addEventListener('click', function() {
                    const filename = this.getAttribute('data-filename');
                    photoFiles = photoFiles.filter(f => f.name !== filename);
                    col.remove();
                    updatePhotoInput();
                });
            };
            reader.readAsDataURL(file);
        });
        
        updatePhotoInput();
    }
    
    // Mettre à jour l'input avec les fichiers
    function updatePhotoInput() {
        const dataTransfer = new DataTransfer();
        photoFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        photoInput.files = dataTransfer.files;
    }
    
    // Gérer les documents par type
    document.querySelectorAll('.document-input').forEach(input => {
        input.addEventListener('change', function() {
            const type = this.getAttribute('data-type');
            const previewContainer = document.querySelector(`.document-preview-${type}`);
            
            Array.from(this.files).forEach(file => {
                // Vérifier la taille (10 Mo)
                if (file.size > 10 * 1024 * 1024) {
                    alert('La taille maximale par document est de 10 Mo');
                    return;
                }
                
                // Afficher le nom du fichier
                const fileDiv = document.createElement('div');
                fileDiv.className = 'd-flex align-items-center justify-content-between p-2 border rounded mb-2 bg-light';
                fileDiv.innerHTML = `
                    <div>
                        <i class="fas fa-file me-2"></i>
                        <small>${file.name}</small>
                        <br>
                        <small class="text-muted">${(file.size / 1024).toFixed(2)} Ko</small>
                    </div>
                `;
                previewContainer.appendChild(fileDiv);
            });
        });
    });
    
    // Supprimer une photo existante
    document.querySelectorAll('.delete-photo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette photo ?')) {
                const photoId = this.getAttribute('data-id');
                const card = this.closest('.col-md-3');
                
                // Appel AJAX pour supprimer la photo
                fetch('<?= base_url("admin/properties/deleteImage") ?>/' + photoId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        card.remove();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        });
    });
    
    // Supprimer un document existant
    document.querySelectorAll('.delete-document-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
                const docId = this.getAttribute('data-id');
                const docDiv = this.closest('.d-flex');
                
                // Appel AJAX pour supprimer le document
                fetch('<?= base_url("admin/properties/deleteDocument") ?>/' + docId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        docDiv.remove();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        });
    });
});
</script>
