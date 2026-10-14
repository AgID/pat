/** QUESTO FILE CONTIENE LE CONFIGURAIZONI DI DEFAULT DA USARE PER GLI SWEET ALERT 2

Nel caso dobbiate modificare dei singoli valori in casi particolari, ricordate che potete scrivere:
Swal({ ...SWAL_DEFAULT_SUCCESS, title: "Nuovo titolo di esempio", showCloseButton: true})

*/


const SWAL_DEFAULT_ERROR = {
    title: '<i class="fas fa-times text-red"></i> Errore',
    text: 'Impossibile effettuare questa operazione',
    confirmButtonText: 'OK',
    confirmButtonColor: '#dc3545',
    allowEnterKey: true,
    showCloseButton: false,
    customClass: {
        title: 'h3 font-weight-bold swal-popup-error'
    }
}

const SWAL_DEFAULT_SUCCESS = {
    title: '<i class="fas fa-check text-green"></i> Operazione effettuata',
    confirmButtonText: 'OK',
    confirmButtonColor: '#198754',
    allowEnterKey: true,
    showCloseButton: false,
    customClass: {
        title: 'h3 font-weight-bold swal-popup-success'
    }
}

const SWAL_CONFIRM = {
    title: 'Conferma operazione',
    text: 'Sei sicuro di voler procedere?',
    showCancelButton: true,
    confirmButtonText: 'Si',
    cancelButtonText: 'Annulla',
    confirmButtonColor: '#198754',
    customClass: {
        title: 'h3 font-weight-bold swal-popup-success'
    }
}

const SWAL_CONFIRM_WARNING = {
    title: 'Attenzione!',
    text: 'Sei sicuro di voler procedere?',
    showCancelButton: true,
    confirmButtonText: 'Si',
    cancelButtonText: 'Annulla',
    confirmButtonColor: '#198754',
    customClass: {
        title: 'h3 font-weight-bold swal-popup-warning'
    }
};