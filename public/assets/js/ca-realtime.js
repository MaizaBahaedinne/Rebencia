/**
 * CA realtime - mise à jour en temps réel du CA
 * Récupère les données du CA payé et affiche la progression vs objectif
 */

class CARealtimeWidget {
    constructor(options = {}) {
        this.options = {
            refreshInterval: options.refreshInterval || 10000, // 10 secondes par défaut
            agencyId: options.agencyId || null,
            period: options.period || this.getCurrentPeriod(),
            containerSelector: options.containerSelector || '#ca-realtime-widget',
            ...options
        };

        this.container = document.querySelector(this.options.containerSelector);
        if (!this.container) {
            console.warn('CA Widget container not found:', this.options.containerSelector);
            return;
        }

        this.init();
    }

    init() {
        this.render();
        this.startRefresh();
    }

    getCurrentPeriod() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        return `${year}-${month}`;
    }

    async fetchCAData() {
        try {
            const params = new URLSearchParams({
                period: this.options.period
            });
            
            if (this.options.agencyId) {
                params.append('agency_id', this.options.agencyId);
            }

            const response = await fetch(`/api/ca/summary?${params}`);
            if (!response.ok) throw new Error('API Error');
            
            const result = await response.json();
            return result.data;
        } catch (error) {
            console.error('Erreur CA:', error);
            return null;
        }
    }

    render() {
        this.container.innerHTML = `
            <div class="ca-widget-loading">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        `;
    }

    async update() {
        const data = await this.fetchCAData();
        if (!data) {
            this.renderError();
            return;
        }

        this.renderContent(data);
    }

    renderContent(data) {
        const progressClass = this.getProgressClass(data.progress_percent);
        const progressColor = this.getProgressColor(data.progress_percent);

        const html = `
            <div class="ca-widget-content">
                <div class="ca-metric">
                    <div class="metric-label">CA Réalisé (HT)</div>
                    <div class="metric-value text-primary">
                        ${this.formatCurrency(data.ca_realized)}
                    </div>
                    <small class="text-muted">Commission payées du mois</small>
                </div>

                ${data.objective_amount > 0 ? `
                <div class="ca-progress mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Objectif</span>
                        <span class="fw-bold">${this.formatCurrency(data.objective_amount)}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-${progressColor}" style="width: ${Math.min(data.progress_percent, 100)}%">
                            <span class="text-white fw-bold">${data.progress_percent}%</span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-${progressClass}">
                            ${this.getProgressText(data.progress_percent)}
                        </small>
                    </div>
                </div>
                ` : ''}

                <div class="ca-timeline mt-3">
                    <small class="text-muted d-block mb-2">Évolution quotidienne</small>
                    <div class="timeline-chart">
                        ${this.renderTimeline(data.timeline)}
                    </div>
                </div>

                <div class="ca-footer text-center mt-3 pt-2 border-top">
                    <small class="text-muted">
                        Mis à jour à ${new Date().toLocaleTimeString('fr-FR')}
                    </small>
                </div>
            </div>
        `;

        this.container.innerHTML = html;
    }

    renderTimeline(timeline) {
        if (!timeline || timeline.length === 0) {
            return '<p class="text-center text-muted py-2"><small>Aucun CA payé</small></p>';
        }

        const maxCA = Math.max(...timeline.map(d => parseFloat(d.ca_day)));
        const scale = maxCA > 0 ? 100 / maxCA : 1;

        return timeline.map(day => {
            const height = Math.max(parseFloat(day.ca_day) * scale, 5);
            return `
                <div class="timeline-bar" style="height: ${height}px;" 
                     title="${this.formatDate(day.day)} - ${this.formatCurrency(day.ca_day)}">
                </div>
            `;
        }).join('');
    }

    renderError() {
        this.container.innerHTML = `
            <div class="alert alert-warning mb-0" role="alert">
                <small><i class="fas fa-exclamation-triangle"></i> Erreur de chargement du CA</small>
            </div>
        `;
    }

    getProgressClass(percent) {
        if (percent >= 100) return 'success';
        if (percent >= 75) return 'info';
        if (percent >= 50) return 'warning';
        return 'danger';
    }

    getProgressColor(percent) {
        if (percent >= 100) return 'success';
        if (percent >= 75) return 'info';
        if (percent >= 50) return 'warning';
        return 'danger';
    }

    getProgressText(percent) {
        if (percent >= 100) return `✓ Objectif dépassé de ${(percent - 100).toFixed(0)}%`;
        if (percent >= 75) return `${percent.toFixed(0)}% de l'objectif atteint`;
        if (percent >= 50) return `${percent.toFixed(0)}% de l'objectif atteint`;
        return `${percent.toFixed(0)}% de l'objectif atteint`;
    }

    formatCurrency(value) {
        return new Intl.NumberFormat('fr-TN', {
            style: 'currency',
            currency: 'TND',
            minimumFractionDigits: 0
        }).format(value);
    }

    formatDate(dateStr) {
        const date = new Date(dateStr + 'T00:00:00');
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    }

    startRefresh() {
        this.update();
        this.refreshTimer = setInterval(() => this.update(), this.options.refreshInterval);
    }

    stopRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
        }
    }

    destroy() {
        this.stopRefresh();
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// Auto-init si présent dans le DOM
document.addEventListener('DOMContentLoaded', () => {
    const widget = document.querySelector('[data-ca-widget]');
    if (widget) {
        const options = {
            agencyId: widget.dataset.agencyId || null,
            period: widget.dataset.period || null,
            refreshInterval: parseInt(widget.dataset.refreshInterval || 10000)
        };
        window.caWidget = new CARealtimeWidget({
            containerSelector: '[data-ca-widget]',
            ...options
        });
    }
});
