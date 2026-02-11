<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-bullseye me-2"></i><?= $title ?></h4>
    <a href="<?= base_url('admin/objectives') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i>Retour
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('admin/objectives/update/' . $objective['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Type d'objectif</label>
                    <input type="text" class="form-control" 
                           value="<?= $objective['type'] === 'personal' ? 'Personnel' : 'Agence' ?>" disabled>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Période</label>
                    <input type="text" class="form-control" 
                           value="<?php
                           list($year, $month) = explode('-', $objective['period']);
                           $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                           echo $monthNames[(int)$month] . ' ' . $year;
                           ?>" disabled>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <?= $objective['type'] === 'personal' ? 'Utilisateur' : 'Agence' ?>
                    </label>
                    <input type="text" class="form-control" 
                           value="<?= $objective['type'] === 'personal' 
                                    ? esc($objective['user_first_name'] . ' ' . $objective['user_last_name'])
                                    : esc($objective['agency_name']) ?>" disabled>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= $objective['status'] === 'active' ? 'selected' : '' ?>>Actif</option>
                        <option value="completed" <?= $objective['status'] === 'completed' ? 'selected' : '' ?>>Terminé</option>
                        <option value="cancelled" <?= $objective['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                    </select>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h5 class="mb-3">Objectifs à atteindre</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Chiffre d'affaires (DT)</label>
                    <input type="number" name="revenue_target" class="form-control" step="0.01" 
                           value="<?= $objective['revenue_target'] ?>">
                    <small class="text-muted">Réalisé: <?= number_format($objective['revenue_achieved'], 2) ?> DT</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre de nouveaux contacts</label>
                    <input type="number" name="new_contacts_target" class="form-control" 
                           value="<?= $objective['new_contacts_target'] ?>">
                    <small class="text-muted">Réalisé: <?= $objective['new_contacts_achieved'] ?></small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Biens pour location</label>
                    <input type="number" name="properties_rent_target" class="form-control" 
                           value="<?= $objective['properties_rent_target'] ?>">
                    <small class="text-muted">Réalisé: <?= $objective['properties_rent_achieved'] ?></small>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Biens pour vente</label>
                    <input type="number" name="properties_sale_target" class="form-control" 
                           value="<?= $objective['properties_sale_target'] ?>">
                    <small class="text-muted">Réalisé: <?= $objective['properties_sale_achieved'] ?></small>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nombre de transactions</label>
                    <input type="number" name="transactions_target" class="form-control" 
                           value="<?= $objective['transactions_target'] ?>">
                    <small class="text-muted">Réalisé: <?= $objective['transactions_achieved'] ?></small>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?= esc($objective['notes']) ?></textarea>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Mettre à jour
                </button>
                <a href="<?= base_url('admin/objectives/refresh/' . $objective['id']) ?>" class="btn btn-info">
                    <i class="fas fa-sync-alt me-1"></i>Actualiser les valeurs
                </a>
                <a href="<?= base_url('admin/objectives') ?>" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
