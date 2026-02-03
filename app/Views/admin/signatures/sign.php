<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-signature text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Document à Signer</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <p><strong>Document:</strong> <?= esc($document['file_name']) ?></p>
                    <p><strong>Type:</strong> <?= esc($document['document_type']) ?></p>
                </div>

                <div class="signature-pad-container">
                    <canvas id="signaturePad" class="signature-canvas"></canvas>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="button" class="btn btn-secondary" id="clearBtn">
                        <i class="fas fa-eraser"></i> Effacer
                    </button>
                    <button type="button" class="btn btn-primary" id="saveBtn">
                        <i class="fas fa-check"></i> Signer & Enregistrer
                    </button>
                </div>

                <div class="mt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">
                            Je certifie que cette signature électronique a la même valeur légale qu'une signature manuscrite
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
const canvas = document.getElementById('signaturePad');
const signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(255, 255, 255)',
    penColor: 'rgb(0, 0, 0)'
});

// Resize canvas
function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
    signaturePad.clear();
}

window.addEventListener('resize', resizeCanvas);
resizeCanvas();

// Clear button
document.getElementById('clearBtn').addEventListener('click', function() {
    signaturePad.clear();
});

// Save button
document.getElementById('saveBtn').addEventListener('click', function() {
    if (signaturePad.isEmpty()) {
        alert('Veuillez signer avant de continuer');
        return;
    }

    if (!document.getElementById('acceptTerms').checked) {
        alert('Veuillez accepter les conditions');
        return;
    }

    const signatureData = signaturePad.toDataURL();
    
    // Save via AJAX
    fetch('<?= base_url('admin/signatures/saveSignature') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'document_id': '<?= $document['id'] ?>',
            'signer_type': 'agent',
            'signer_name': '<?= session()->get('first_name') . " " . session()->get('last_name') ?>',
            'signer_email': '<?= session()->get('email') ?>',
            'signature_data': signatureData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Signature enregistrée avec succès');
            window.location.href = '<?= base_url('admin/documents/' . $document['transaction_id']) ?>';
        } else {
            alert('Erreur lors de l\'enregistrement');
        }
    });
});
</script>

<style>
.signature-pad-container {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 10px;
    background: #f8f9fa;
}

.signature-canvas {
    width: 100%;
    height: 300px;
    background: white;
    border-radius: 5px;
    touch-action: none;
}
</style>

<?= $this->endSection() ?>
