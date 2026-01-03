// Fonctions pour le workflow des échéances
(function() {
    'use strict';
    
    // Initialiser l'objet global
    window.installmentWorkflow = window.installmentWorkflow || { currentInstallmentId: null };
    
    // Fonction pour marquer comme payée
    window.handleMarkAsPaid = function(installmentId, orderId, buttonElement) {
        console.log('handleMarkAsPaid called', { installmentId, orderId, buttonElement });
        
        try {
            if (!installmentId || !orderId) {
                console.error('Missing data:', { installmentId, orderId });
                alert('Erreur: Données manquantes');
                return false;
            }
            
            console.log('Showing confirmation dialog...');
            const confirmed = confirm('Êtes-vous sûr de vouloir marquer cette échéance comme payée ?');
            console.log('Confirmation result:', confirmed);
            
            if (!confirmed) {
                console.log('User cancelled');
                return false;
            }
            
            // Afficher le spinner
            console.log('Updating UI...');
            if (buttonElement) {
                buttonElement.disabled = true;
                buttonElement.style.opacity = '0.6';
                const icon = document.getElementById('mark-paid-icon-' + installmentId);
                const text = document.getElementById('mark-paid-text-' + installmentId);
                const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                if (icon) icon.style.display = 'none';
                if (text) text.textContent = 'Traitement...';
                if (spinner) spinner.style.display = 'block';
            }
            
            const url = '/capocopadmin/orders/' + orderId + '/installments/' + installmentId + '/mark-paid';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            console.log('Making fetch request to:', url);
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Missing');
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => {
                console.log('Response received:', response.status, response.statusText);
                if (!response.ok) {
                    const errorText = response.statusText || 'Network response was not ok';
                    console.error('Response not OK:', response.status, errorText);
                    return response.text().then(text => {
                        console.error('Response body:', text);
                        throw new Error(errorText + ': ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Succès: ' + (data.message || 'Échéance marquée comme payée avec succès'));
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    console.error('Request failed:', data);
                    if (buttonElement) {
                        buttonElement.disabled = false;
                        buttonElement.style.opacity = '1';
                        const icon = document.getElementById('mark-paid-icon-' + installmentId);
                        const text = document.getElementById('mark-paid-text-' + installmentId);
                        const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                        if (icon) icon.style.display = 'block';
                        if (text) text.textContent = 'Marquer payée';
                        if (spinner) spinner.style.display = 'none';
                    }
                    alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.style.opacity = '1';
                    const icon = document.getElementById('mark-paid-icon-' + installmentId);
                    const text = document.getElementById('mark-paid-text-' + installmentId);
                    const spinner = document.getElementById('mark-paid-spinner-' + installmentId);
                    if (icon) icon.style.display = 'block';
                    if (text) text.textContent = 'Marquer payée';
                    if (spinner) spinner.style.display = 'none';
                }
                alert('Erreur: Une erreur est survenue lors du traitement. Vérifiez la console pour plus de détails.');
            });
            
            return false;
        } catch (error) {
            console.error('handleMarkAsPaid error:', error);
            alert('Erreur: ' + error.message);
            return false;
        }
    };
    
    // Fonction pour ouvrir le modal
    window.handleUpdateStatus = function(installmentId, currentStatus, orderId) {
        console.log('handleUpdateStatus called', { installmentId, currentStatus, orderId });
        
        if (!installmentId || !orderId) {
            alert('Erreur: Données manquantes');
            return false;
        }
        
        window.installmentWorkflow.currentInstallmentId = installmentId;
        window.installmentWorkflow.orderId = orderId;
        
        const modal = document.getElementById('statusModal');
        const statusSelect = document.getElementById('statusSelect');
        const paidAtContainer = document.getElementById('paidAtContainer');
        const paidAtInput = document.getElementById('paidAtInput');
        
        if (!modal || !statusSelect) {
            alert('Erreur: Modal non trouvé');
            return false;
        }
        
        statusSelect.value = currentStatus || 'pending';
        
        if (currentStatus === 'paid') {
            paidAtContainer.style.display = 'block';
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            paidAtInput.value = now.toISOString().slice(0, 16);
        } else {
            paidAtContainer.style.display = 'none';
        }
        
        // Réinitialiser l'event listener du select
        const newStatusSelect = statusSelect.cloneNode(true);
        statusSelect.parentNode.replaceChild(newStatusSelect, statusSelect);
        const newSelect = document.getElementById('statusSelect');
        if (newSelect) {
            newSelect.addEventListener('change', function() {
                if (this.value === 'paid') {
                    paidAtContainer.style.display = 'block';
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    paidAtInput.value = now.toISOString().slice(0, 16);
                } else {
                    paidAtContainer.style.display = 'none';
                }
            });
        }
        
        modal.style.display = 'flex';
        return false;
    };
    
    // Fonction pour mettre à jour le statut
    window.updateStatus = function(event) {
        if (event) event.preventDefault();
        
        if (!window.installmentWorkflow || !window.installmentWorkflow.currentInstallmentId || !window.installmentWorkflow.orderId) {
            alert('Erreur: Données manquantes');
            return false;
        }
        
        const statusSelect = document.getElementById('statusSelect');
        const paidAtInput = document.getElementById('paidAtInput');
        const status = statusSelect ? statusSelect.value : null;
        const paidAt = status === 'paid' && paidAtInput ? paidAtInput.value : null;
        
        const submitButton = document.getElementById('statusFormSubmit');
        if (!submitButton) {
            alert('Erreur: Bouton de soumission non trouvé');
            return false;
        }
        
        const originalText = submitButton.textContent;
        
        // Afficher le spinner
        submitButton.disabled = true;
        submitButton.style.opacity = '0.6';
        submitButton.style.cursor = 'not-allowed';
        submitButton.innerHTML = '<svg style="display: inline-block; width: 1rem; height: 1rem; animation: spin 1s linear infinite; margin-right: 0.5rem;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Traitement...';
        
        const url = '/capocopadmin/orders/' + window.installmentWorkflow.orderId + '/installments/' + window.installmentWorkflow.currentInstallmentId + '/update-status';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                status: status,
                paid_at: paidAt
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Succès: ' + (data.message || 'Statut de l\'échéance mis à jour avec succès'));
                window.closeStatusModal();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                submitButton.disabled = false;
                submitButton.style.opacity = '1';
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = originalText;
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            submitButton.style.cursor = 'pointer';
            submitButton.textContent = originalText;
            alert('Erreur: Une erreur est survenue lors du traitement');
        });
        
        return false;
    };
    
    // Fonction pour fermer le modal
    window.closeStatusModal = function() {
        const modal = document.getElementById('statusModal');
        if (modal) {
            modal.style.display = 'none';
        }
        window.installmentWorkflow.currentInstallmentId = null;
    };
    
    // Fermer le modal en cliquant en dehors
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('statusModal');
        if (modal && e.target === modal) {
            window.closeStatusModal();
        }
    });
    
    // Attacher des event listeners de secours aux boutons
    function attachButtonListeners() {
        // Event listeners pour les boutons "Marquer payée"
        document.querySelectorAll('.mark-paid-button').forEach(button => {
            const installmentId = button.getAttribute('data-installment-id');
            const orderId = button.getAttribute('data-order-id');
            
            // Supprimer les anciens listeners pour éviter les doublons
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Attacher le nouveau listener
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Button clicked via event listener', { installmentId, orderId });
                if (window.handleMarkAsPaid) {
                    window.handleMarkAsPaid(installmentId, orderId, this);
                } else {
                    console.error('handleMarkAsPaid function not found!');
                    alert('Erreur: Fonction non disponible. Veuillez recharger la page.');
                }
                return false;
            });
        });
        
        // Event listeners pour les boutons "Modifier statut"
        document.querySelectorAll('.update-status-button').forEach(button => {
            const installmentId = button.getAttribute('data-installment-id');
            const status = button.getAttribute('data-installment-status');
            const orderId = button.getAttribute('data-order-id');
            
            // Supprimer les anciens listeners pour éviter les doublons
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            // Attacher le nouveau listener
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Update status button clicked via event listener', { installmentId, status, orderId });
                if (window.handleUpdateStatus) {
                    window.handleUpdateStatus(installmentId, status, orderId);
                } else {
                    console.error('handleUpdateStatus function not found!');
                    alert('Erreur: Fonction non disponible. Veuillez recharger la page.');
                }
                return false;
            });
        });
        
        console.log('Button listeners attached:', {
            markPaid: document.querySelectorAll('.mark-paid-button').length,
            updateStatus: document.querySelectorAll('.update-status-button').length
        });
    }
    
    // Attacher les listeners après le chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachButtonListeners);
    } else {
        // DOM déjà chargé, attacher immédiatement et après un court délai
        attachButtonListeners();
        setTimeout(attachButtonListeners, 100);
        setTimeout(attachButtonListeners, 500);
    }
    
    console.log('Installment workflow functions loaded', {
        handleMarkAsPaid: typeof window.handleMarkAsPaid,
        handleUpdateStatus: typeof window.handleUpdateStatus,
        updateStatus: typeof window.updateStatus,
        closeStatusModal: typeof window.closeStatusModal
    });
})();

