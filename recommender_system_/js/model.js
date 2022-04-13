let listaLingue=[];
let listaDifficolta=[];
let listaDurate=[];
let listaFormati=[];
let listaTipi=[];
let listaDiscipline=[];
let etaMinima=[];
let etaMassima=[];

class Model{

    //METODI PER AGGIUNGERE LE PREFERENZE SCELTE DALL'UTENTE
    addLanguage(lingua){
        listaLingue.push(lingua);
    }

    addDifficulty(difficolta){
        listaDifficolta.push(difficolta);
    }

    addDuration(durata){
        listaDurate.push(durata);
    }

    addFormat(formato){
        listaFormati.push(formato);
    }

    addType(tipo){
        listaTipi.push(tipo);
    }

    addDiscipline(disciplina){
        listaDiscipline.push(disciplina);
    }

    addMinimumAge(min){
        etaMinima=[];
        etaMinima.push(min);
    }

    addMaximumAge(max){
        etaMassima=[];
        etaMassima.push(max);
    }

    //METODO PER AZZERARE LE PREFERENZE
    clearPreferences(){
        listaLingue=[];
        listaDifficolta=[];
        listaDurate=[];
        listaFormati=[];
        listaTipi=[];
        listaDiscipline=[];
        etaMinima=[];
        etaMassima=[];
    }

    //METODI PER RITORNARE LE LISTE DELLE VARIE PREFERENZE
    getListaLingue(){
        return listaLingue;
    }

    getListaDifficolta(){
        return listaDifficolta;
    }

    getListaDurate(){
        return listaDurate;
    }

    getListaFormati(){
        return listaFormati;
    }

    getListaTipi(){
        return listaTipi;
    }

    getListaDiscipline(){
        return listaDiscipline;
    }

    getEtaMinima(){
        return etaMinima[0];
    }

    getEtaMassima(){
        return etaMassima[0];
    }

}

function setModel(model) {
    this.model = model;
}

function getInstance() {
    return model;
}