//VARIABILI CHE MEMORIZZANO IL RANGE DI ETA' SCELTO DALL'UTENTE
var rangeEta;
var etaMinimaRisorsa;
var etaMassimaRisorsa;

//VARIABILE PER MEMORIZZARE GLOBALMENTE TUTTE LE RISORSE SUGGERITE DAL SISTEMA
var recommendedResources;
var ratingResources;

//VARIABILE PER MEMORIZZARE I NOMI DELLE SEZIONI
var nameSections=[];
var moduleResource=[];

//VARIABILE PER MEMORIZZARE LA RISORSA CHE SI STA SELEZIONANDO
var selectedResource;
var nameSelectedResource;

//VARIABILI PER GESTIRE LA CIRCULAR PROGRESSBAR
var circle;
var colorProgress;
var utilityResource;

//VARIABILE PER MEMORIZZARE LE DISCIPLINE
var disciplines;


$(function() {

    //ALL'ULTIMA PAGINA QUANDO SI SCHIACCIA SU 'VAI AL CORSO' VIENE CERCATO L'ID DEL CORSO CREATO E SI VIENE REINDIRIZZATI ALLA PAGINA DEL CORSO
    $("#bottoneVaiAlCorso").click(function(){

        vaiAlCorso();

    });


    //NELL'ANTEPRIMA DELLE RISORSE QUANDO SI RIDIMENSIONA LA FINESTRA VIENE ADATTATA IN MODO RESPONSIVE
    $("div[name='rowSmallDesktop']").css("display","none");
    $("#descriptionSmartphone").css("display","none");
    window.onresize = function(){
        if($(window).width()<800){
            $("div[name='rowSmallDesktop']").css("display","");
            $("#descriptionSmartphone").css("display","");
            $("div[name='rowFullDesktop']").css("display","none");
        }else{
            $("div[name='rowSmallDesktop']").css("display","none");
            $("#descriptionSmartphone").css("display","none");
            $("div[name='rowFullDesktop']").css("display","");
        }
    };
    

    $("#btnSeparaRisorse").click(function(){

        var cont=0;
        var moduleSelected=[];

        //CONTROLLO CHE ALMENO UN MODULO SIA SELEZIONATO
        for(i=0;i<nameSections.length;i++){
            let nome=nameSections[i];
            if($("#rowSeparaRisorse[name='"+nome+"']").attr("selezionato")=="true"){
                cont++;
                moduleSelected.push(i);
            }
        }



        if(cont>0){
            $(".btn-close").click();


            for(i=0;i<moduleSelected.length;i++){
                let moduloSelezionato=moduleSelected[i];
                moduleResource[moduloSelezionato].idrisorse.push(selectedResource);
                moduleResource[moduloSelezionato].nomirisorse.push(nameSelectedResource);
            }

            console.log(moduleResource);

        }else{
            erroreModulo();
        }

    });

    //SCROLL DOWN IN BASE ALLA PAGINA
    if(window.location.href.includes("step1.php")){
        window.scrollTo(0, 240);
    }else if(window.location.href.includes("step2.php")){
        window.scrollTo(0, 240);
    }
    else if(window.location.href.includes("step3.php")){
        window.scrollTo(0, 230);
        circle = new CircularProgressBar("pie");
        circle.initial();
    }
    else if(window.location.href.includes("step4.php")){
        window.scrollTo(0, 230);
    }
    else if(window.location.href.includes("step5.php")){
        window.scrollTo(0, 230);
    }

    //ALLA PAGINA INIZIALE PULISCO LA SESSION STORAGE
    if(window.location.href.includes("index.php")){

        window.scrollTo(0, 230);
        sessionStorage.clear();

    }


    //QUANDO CHIUDO L'ANTEPRIMA CHIUDO ANCHE LA FINESTRA DELLA SPIEGAZIONE DELLA RISORSA
    $(".btn-close").click(function(){

        $("#rowExplanation").css("display","none");

    });

    //QUANDO SI CLICCA SULL'INCONA DI INFO NELL'ANTEPRIMA DELLA RISORSA SI APRE LA SPIEGAZIONE DELLA REGOLA
    $("#infoRegola").click(function(){
        
        if($("#rowExplanation").css("display")=="none"){
            $("#rowExplanation").css("display","");
        }else{
            $("#rowExplanation").css("display","none");
        }


    });

    //RIPRISTINA LE RISORSE SELEZIONATE ALLO STEP 3
    $("#bottoneRipristinaRisorse").click(function(){

        for(i=0;i<recommendedResources.length;i++){

            let obj=recommendedResources[i];
            let title=obj.title;

            $("input[name='"+title+"']").removeAttr("checked");

            
        }


        for(i=0;i<moduleResource.length;i++){
            moduleResource[i].idrisorse=[];
            moduleResource[i].nomirisorse=[];
        }

        console.log(moduleResource);

    })

    //QUANDO SI AGGIUNGE LA RISORSA DALL'ANTEPRIMA VIENE SELEZIONATA LA SUA CHECKBOX
    $("#btnAggiungiRisorsa").click(function(){

        let name=$(this).prop("name");

        if($(this).html()=="Aggiungi"){
            //SI STA AGGIUNGENDO LA RISORSA

            caricaSezioni();

            $("#btnShowModalSections").click();
    
            $("input[name='"+name+"']").click();

        }else{
            //SI STA RIMUOVENDO LA RISORSA
            $("input[name='"+name+"']").click();
            $(".btn-close").click();

            for(i=0;i<moduleResource.length;i++){
                let currentModule=moduleResource[i];
    
                for(j=0;j<currentModule.idrisorse.length;j++){
                    if(currentModule.nomirisorse[j]==name){
                        currentModule.idrisorse.splice(j,1);
                        currentModule.nomirisorse.splice(j,1);
                    }
                }
    
            }
    

        }
        
        

    });

    //NASCONDE ALL'INIZIO LA SCHERMATA DI CARICAMENTO
    if(!window.location.href.includes("step3.php")){

        $("div[name='contenitoreCaricamento']").hide();

    }

    //FUNZIONE CHE GESTISCE IL DOPPIO SLIDER
    $(".js-range-slider").on("change", function() {
        var $inp = $(this);
        rangeEta = $inp.prop("value"); // reading input value

        etaMinimaRisorsa=rangeEta.split(";")[0];
        etaMassimaRisorsa=rangeEta.split(";")[1];
        

    });


    $(".js-range-slider").ionRangeSlider({
        type: "double",
        skin: "round",
        from: 1,
        to: 100,
        min: 1,
        max: 100
    });

    //PER ABILITARE I TOOLTIP IN TUTTE LE PAGINE
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    //ALL'AVVIO NASCONDO LA RIGA CHE CORRISPONDE AL NUMERO DELLE SEZIONI
    $("#rowNumeroSezioni").hide();


    //QUANDO SI CLICCA SU RIPRISTINA NELLA PRIMA SCHERMATA AZZERA TUTTI I CAMPI
    $("#bottoneRipristinaCorso").click(function() {

        $('#exampleFormControlInput1').val("");
        $('#selectCategoria').val("Categoria");
        $('#selectNumeroSezioni').val("Numero");
        showSubcategory("Sottocategoria");
        $('#flexSwitchCheckDefault').prop('checked', false);
        visualizzaSezioni($("#flexSwitchCheckDefault"));

    });

    //QUANDO SI CLICCA SU RIPRISTINA NELLA SECONDA SCHERMATA AZZERA TUTTI GLI ARGOMENTI SELEZIONATI
    $("#bottoneRipristinaArgomenti").click(function() {

        let argomenti = $("#myUL").children();

        for (i = 0; i < argomenti.length; i++) {
            li = argomenti[i];
            a = li.children[0];
            svg = a.children[0];
            confirmIcon = a.children[0].children[1];

            a.style.backgroundColor = "#f6f6f6";
            a.style.color = "black";
            confirmIcon.setAttribute("fill", "#707070");
            svg.style.visibility = "hidden";
            a.setAttribute("checked", "false");


        }

        document.getElementById("inputSearchArgument").value = "";
        filtraArgomenti();

    });

    //QUANDO SI CLICCA SU RIPRISTINA NELLA TERZA SCHERMATA AZZERA TUTTE LE PREFERENZE SELEZIONATE
    $("#bottoneRipristinaPreferenze").click(function() {

        let languages = document.querySelector("ul[name='listaLingue']");

         for (i = 0; i < languages.children.length; i++) {
             let currentLanguage = languages.children[i].children[0].children[0];
             let nameLanguage=currentLanguage.getAttribute("name");

             $("input[name='"+nameLanguage+"']").removeAttr("checked");
         }

         let difficulties = document.querySelector("ul[name='listaDifficolta']");

         for (i = 0; i < difficulties.children.length; i++) {
             let currentDifficulty = difficulties.children[i].children[0].children[0];
             let nameDifficulty=currentDifficulty.getAttribute("name");

             $("input[name='"+nameDifficulty+"']").removeAttr("checked");
         }

         let times = document.querySelector("ul[name='listaTempo']");

         for (i = 0; i < times.children.length; i++) {
             let currentTime = times.children[i].children[0].children[0];
             let nameTime=currentTime.getAttribute("name");

             $("input[name='"+nameTime+"']").removeAttr("checked");
         }

         let formats = document.querySelector("ul[name='listaFormato']");

         for (i = 0; i < formats.children.length; i++) {
             let currentFormat = formats.children[i].children[0].children[0];
             let nameFormat=currentFormat.getAttribute("name");

             $("input[name='"+nameFormat+"']").removeAttr("checked");
         }

         let types = document.querySelector("ul[name='listaTipo']");

         for (i = 0; i < types.children.length; i++) {
             let currentType = types.children[i].children[0].children[0];
             let nameType=currentType.getAttribute("name");

             $("input[name='"+nameType+"']").removeAttr("checked");
         }


         $(".js-range-slider").data("ionRangeSlider").reset();



    });

    //QUANDO SI CLICCA SUL BOTTONE ANNULLA APPARE UN DIALOG PER USCIRE DALL'RS
    $('#bottoneAnnulla').click(() => {
        Swal.fire({
            title: 'Sei sicuro?',
            text: "Tutti i dati inseriti andranno persi",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Si',
            cancelButtonText: 'Cancella'
        }).then((result) => {
            if (result.isConfirmed) {

                window.location.href = "../index.php"

            }
        })
    });


    //QUANDO SELEZIONO UN ARGOMENTO DAI RISULTATI LO EVIDENZIO
    $(".result").click(function() {

        let svg = this.children[0];
        let confirmIcon = svg.children[1];
        let nameDiscipline = this.getAttribute("id");

        if (this.getAttribute("checked") == "false") {
            //L'ELEMENTO NON E' SELEZIONATO QUINDI LO SELEZIONO
            svg.style.visibility = "visible";
            this.style.backgroundColor = "#676766";
            this.style.color = "#FCC63D";
            confirmIcon.setAttribute("fill", "#FCC63D");
            this.setAttribute("checked", "true");
            $("input[name='" + nameDiscipline + "']").val("true");

        } else {
            //L'ELEMENTO E' GIA' SELEZIONATO QUINDI LO DISSELEZIONO
            svg.style.visibility = "hidden";
            this.style.backgroundColor = "#f6f6f6";
            this.style.color = "black";
            confirmIcon.setAttribute("fill", "#707070");
            this.setAttribute("checked", "false");
            $("input[name='" + nameDiscipline + "']").val("false");
        }


    });

});

//QUANDO LA CHECKBOX E' ATTIVA ALLORA MI MOSTRA LA RIGA CORRISPONDENTE AL NUMERO DELLE SEZIONI
function visualizzaSezioni(checkbox) {
    if (checkbox.checked) {

        $("#rowNumeroSezioni").show();


    } else {
        $("#rowNumeroSezioni").hide();
    }
}

//FUNZIONA CHE CONTROLLA I CAMPI INSERITI NELLA PRIMA SCHERMATA
function check() {

    $("#btnShowSectionsModal").click()

    let nomeCorso = $("#exampleFormControlInput1").val();
    let categoriaCorso = $("#selectCategoria").val();
    let vuoto = "Categoria";

    while($("#btnShowSectionsModal").data('clicked')==false){};


    if (nomeCorso == "" && categoriaCorso == vuoto) {
        Swal.fire({
            icon: 'error',
            title: 'Impossibile procedere',
            text: 'Il nome del corso e la categoria sono dati obbligatori',
            confirmButtonColor: '#dc3545',
        })
        return false;
    } else if (nomeCorso == "") {
        Swal.fire({
            icon: 'error',
            title: 'Impossibile procedere',
            text: 'Inserisci il nome del corso',
            confirmButtonColor: '#dc3545',
        })
        return false;
    } else if (categoriaCorso == vuoto) {
        Swal.fire({
            icon: 'error',
            title: 'Impossibile procedere',
            text: 'Inserisci la categoria del corso',
            confirmButtonColor: '#dc3545',
        })
        return false;
    }



    $("div[name='contenitoreFase1']").hide();
    $("div[name='contenitoreCaricamento']").show();
    memorizzaInfo();
    return true;


    
}

//FUNZIONA CHE MOSTRA I RISULTATI IN BASE A QUELLO CHE CERCA L'UTENTE
function filtraArgomenti() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("inputSearchArgument");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");


    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }


}

//FUNZIONE CHE AGGIUNGE ALLA HASHMAP LE DISCIPLINE CHE SONO STATE SELEZIONATE NELLA SECONDA SCHERMATA
function addDiscipline(name, value) {
    disciplines.set(name, value);
}

//FUNZIONE CHE MOSTRA LA SOTTOCATEGORIA SE ESISTE
function showSubcategory(category) {


    if (category == "Abilità informatiche") {
        //PRIMA AZZERO TUTTI I VALORI PRECEDENTI
        $("option[name='nuovaCategoria']").css("display", "none");
        $("option[name='nuovaCategoria2']").css("display", "none");
        $("option[name='SIAM']").css("display", "none");


        //POI SETTO IN BASE ALLA CATEGORIA
        $("#selectSottocategoria").removeAttr("disabled");
        $("#selectSottocategoria").val("Sottocategoria");
        $("option[name='SIAM']").css("display", "");
        
    } else if (category == "Competenze in Economia") {
        //PRIMA AZZERO TUTTI I VALORI PRECEDENTI
        $("option[name='nuovaCategoria']").css("display", "none");
        $("option[name='nuovaCategoria2']").css("display", "none");
        $("option[name='SIAM']").css("display", "none");

        //POI SETTO IN BASE ALLA CATEGORIA
        $("#selectSottocategoria").removeAttr("disabled");
        $("#selectSottocategoria").val("Sottocategoria");
        $("option[name='nuovaCategoria']").css("display", "");
        $("option[name='nuovaCategoria2']").css("display", "");
        
    } else {
        $("option[name='nuovaCategoria']").css("display", "none");
        $("option[name='nuovaCategoria2']").css("display", "none");
        $("option[name='SIAM']").css("display", "none");
        $("#selectSottocategoria").attr("disabled", "");
        $("#selectSottocategoria").val("Sottocategoria");
    }


}

//FUNZIONE CHE SELEZIONA LA CHECKBOX QUANDO SI SCHIACCIA SULLA SCRITTA DELL'ELEMENTO
function clickCheckbox(element) {

    let name = element.getAttribute("name");
    let checkValue = document.querySelector("input[name='" + name + "']").getAttribute("value");

    if ($("input[name='" + name + "']").is(':checked')){
        //SE E' GIA' SELEZIONATA ALLORA LA DISATTIVO
        $("input[name='" + name + "']").removeAttr("checked");
    }else{
        //SE NON E' SELEZIONATA LA ATTIVO
        $("input[name='" + name + "']").prop("checked",true);
    }

    event.stopPropagation();

}


//FUNZIONE CHE SALVA IN ARRAY JAVASCRIPT TUTTE LE PREFERENZE SCELTE DALL'UTENTE NELLA TERZA SCHERMATA
function memorizzaPreferenze(){

    var lingue=$("a[name='dropdown-language']");
    var difficolta=$("a[name='dropdown-difficulty']");
    var durate=$("a[name='dropdown-duration']");
    var formati=$("a[name='dropdown-format']");
    var tipi=$("a[name='dropdown-type']");

    var nomeLingua;
    var nomeDifficolta;
    var nomeDurata;
    var nomeFormato;
    var nomeTipo;

    var inputLingua;
    var inputDifficolta;
    var inputDurata;
    var inputFormato;
    var inputTipo;

    var arrayLingue=[];
    var arrayDifficolta=[];
    var arrayDurate=[];
    var arrayFormati=[];
    var arrayTipi=[];
    var arrayEtaMinima=[];
    var arrayEtaMassima=[];

    for(i=0;i<lingue.length;i++){
        nomeLingua=lingue[i].text;
        inputLingua=$("input[name='"+nomeLingua+"']");

        if(inputLingua.is(':checked')){
            arrayLingue.push(nomeLingua);
        }
    }

    for(i=0;i<difficolta.length;i++){
        nomeDifficolta=difficolta[i].text;
        inputDifficolta=$("input[name='"+nomeDifficolta+"']");

        if(inputDifficolta.is(':checked')){
            arrayDifficolta.push(nomeDifficolta);
        }
    }

    for(i=0;i<durate.length;i++){
        nomeDurata=durate[i].text;
        inputDurata=$("input[name='"+nomeDurata+"']");

        if(inputDurata.is(':checked')){
            arrayDurate.push(nomeDurata);
        }
    }

    for(i=0;i<formati.length;i++){
        nomeFormato=formati[i].text;
        inputFormato=$("input[name='"+nomeFormato+"']");

        if(inputFormato.is(':checked')){
            arrayFormati.push(nomeFormato);
        }
    }

    for(i=0;i<tipi.length;i++){
        nomeTipo=tipi[i].text;
        inputTipo=$("input[name='"+nomeTipo+"']");

        if(inputTipo.is(':checked')){
            arrayTipi.push(nomeTipo);
        }

    }

    arrayEtaMinima.push(etaMinimaRisorsa);
    arrayEtaMassima.push(etaMassimaRisorsa);

    sessionStorage.setItem("languages",JSON.stringify(arrayLingue));
    sessionStorage.setItem("difficulties",JSON.stringify(arrayDifficolta));
    sessionStorage.setItem("duration",JSON.stringify(arrayDurate));
    sessionStorage.setItem("formats",JSON.stringify(arrayFormati));
    sessionStorage.setItem("types",JSON.stringify(arrayTipi));
    sessionStorage.setItem("minimumage",JSON.stringify(arrayEtaMinima));
    sessionStorage.setItem("maximumage",JSON.stringify(arrayEtaMassima));


}

//FUNZIONE CHE FA LA CHIAMATA API ALL'ALBERO PER RICEVERE GLI ID DELLE RISORSE CONSIGLIATE
function getRecommendations(){

    saveNameSections();
    
    $("div[name='contenitoreCaricamento']").show();

    
    var elencoDiscipline=JSON.parse(sessionStorage.getItem("disciplines"));
    var elencoLingue=JSON.parse(sessionStorage.getItem("languages"));
    var elencoDifficolta=JSON.parse(sessionStorage.getItem("difficulties"));
    var elencoDurata=JSON.parse(sessionStorage.getItem("duration"));
    var elencoFormato=JSON.parse(sessionStorage.getItem("formats"));
    var elencoTipo=JSON.parse(sessionStorage.getItem("types"));
    var elencoEtaMinima=JSON.parse(sessionStorage.getItem("minimumage"));
    var elencoEtaMassima=JSON.parse(sessionStorage.getItem("maximumage"));

    let JsonObject={};

    if(elencoEtaMinima!="" || elencoEtaMassima!=""){

        JsonObject={
            disciplines:elencoDiscipline,
            language: elencoLingue,
            difficulty: elencoDifficolta,
            duration: elencoDurata,
            format: elencoFormato,
            type: elencoTipo,
            min_age:parseInt(elencoEtaMinima[0]),
            max_age:parseInt(elencoEtaMassima[0]),
            request_type: "recommend"
        }

    }else{

        JsonObject={
            disciplines:elencoDiscipline,
            language: elencoLingue,
            difficulty: elencoDifficolta,
            duration: elencoDurata,
            format: elencoFormato,
            type: elencoTipo,
            min_age:1,
            max_age:100,
            request_type: "recommend"
        }

    }

    console.log(JSON.stringify(JsonObject));


    $.ajax({
        type: "POST",
        url: "https://europe-west1-whoteach-dev.cloudfunctions.net/recommender_tree2",
        data: JSON.stringify(JsonObject),
        async:false,
        success: function (result) {

            console.log(result);

            let arrayRisorse=[];
            let utilitàRisorse=[];
            let regolaRisorse=[];

            for(i=0;i<result.id.length;i++){
                arrayRisorse.push(result.id[i]);
                utilitàRisorse.push(result.utility[i]);
                regolaRisorse.push(JSON.stringify(result.satisfied_preferences[i]));
            }

            memorizzaValutazioni(arrayRisorse);
            memorizzaRisorse(arrayRisorse,utilitàRisorse,regolaRisorse);
            
       
        },
        error: function (error) {
            console.error(error);
        }
    });

}

//FUNZIONE CHE MEMORIZZA LE DISCIPLINE SELEZIONATE DALL'UTENTE NELLA SECONDA SCHERMATA
function memorizzaDiscipline(){

    let risultati=$(".result");
    let arrayDiscipline=[];

    for(i=0;i<risultati.length;i++){
        if(risultati[i].getAttribute("checked")=="true"){
            arrayDiscipline.push(risultati[i].text);
        }
    }

    sessionStorage.setItem("disciplines",JSON.stringify(arrayDiscipline));

}

//FUNZIONE CHE MOSTRA LA SCHERMATA DI CARICAMENTO
function showSpinner(step){

    if(step==1){

        /*var cont=0;

        for(i=0;i<disciplines.length;i++){
            var title=disciplines[i];

            if($("a[id='"+title+"']").attr("checked")=="true"){
                cont++;
            }

        }

        if(cont==0){
            Swal.fire({
                icon: 'error',
                title: 'Impossibile procedere',
                text: 'Seleziona almeno una disciplina',
                confirmButtonColor: '#dc3545',
            })
            return false;
        }*/

        $("div[name='contenitoreFase2']").hide();
        $("div[name='contenitoreCaricamento']").show();
        memorizzaDiscipline();
    } else if(step==2){
        
        $("div[name='contenitoreFase3']").hide();
        $("div[name='contenitoreCaricamento']").show();
        memorizzaPreferenze();
    } else if(step==3){

        var cont=0;

        for(i=0;i<recommendedResources.length;i++){
            var obj=recommendedResources[i];
            var title=obj.title;

            if($("input[name='"+title+"']").is(":checked")){
                cont++;
            }

        }

        if(cont==0){
            Swal.fire({
                icon: 'error',
                title: 'Impossibile procedere',
                text: 'Seleziona almeno una risorsa',
                confirmButtonColor: '#dc3545',
            })
            return false;
        }

        $("div[name='contenitoreFase4']").css("display","none");
        $("div[name='contenitoreCaricamento']").show();
        memorizzaRisorseSelezionate();
    } else if(step==4){
        $("div[name='contenitoreFase5']").css("display","none");
        $("div[name='contenitoreCaricamento']").show();
        memorizzaInformazioniCorso();
    }

    
    return true;

}

//FUNZIONE CHE PASSA AL PHP GLI ID DELLE RISORSE RICEVUTE DALL'ALBERO E RICEVE INTERAMENTE LE RISORSE
function memorizzaRisorse(risorse,utilità,regole){

    var jsondata={'arrayRisorse': risorse,'utilitàRisorse': utilità,'regolaRisorse': regole};

    $.ajax({
        type: "POST",
        url: "resources_json.php",
        data: jsondata,
        aysnc:false,
        success: function(result){

            recommendedResources=jQuery.parseJSON(result);
            printResources(jQuery.parseJSON(result));
        },
        error: function (error) {
            console.error(error);
            console.log("non è disponibile nessuna risorsa per le preferenze selezionate");

        }
    });

}

//FUNZIONE CHE NASCONDE LA SCHERMATA DOPO CHE SI HANNO STAMPATO LE RISORSE
function hideLoading(){

    $("div[name='contenitoreCaricamento']").hide();
    $("div[name='contenitoreFase4']").css("display","");
    
}

//FUNZIONE CHE STAMPA LE RISORSE DOOPO LA RISPOSTA RICEVUTA DALL'ALBERO
function printResources(risorse){


    for(i=0;i<risorse.length;i++) {

        var obj=risorse[i];


        var stars=obj.average_rating;
        var notstars=5-obj.average_rating;
        var ratingResource="";
        var resource_image;
        var id=obj.id;

        if(obj.resource_image=="unknown"){
            resource_image="img/resource.png";
        }else{
            resource_image=obj.resource_image;
        }

        for(k=0;k<stars;k++){
            ratingResource.concat("<span class='fa fa-star checked'></span>");
        }

        for(j=0;j<notstars;j++){
            ratingResource.concat("<span class='fa fa-star'></span>");
        }
        
        var currentResource="<div class='row' id='rowResource'>"
	
	
        +"<div class='col-auto' id='colResourceImage' name='"+obj.title+"'>"

            +"<div class='resourceImageContainer' style='position: relative;' name='"+obj.title+"'  id='"+id+"' onclick='selectResource(this);' data-bs-toggle='modal' data-bs-target='#exampleModal'>"
                +"<img src='"+resource_image+"' class='resourceImage'>"
                +"<div class='middleResourceImage'>"
                    +"<div class='textMostraAnteprima'>MOSTRA</div>"
                +"</div>"
            +"</div>"




        +"</div>"

        +"<div class='col' id='colInfoResource'>"

            +"<div class='row' id='rowInfoResource'>"

                +"<div class='col-12' style='height:auto'>"

                    +"<h4 class='titleResource'>"+obj.title+"</h4>"
                    
                +"</div>"

            +"</div>"

            +"<div class='row' id='rowInfoResource'>"

                +"<div class='col-12'>"

                        +"<p class='infoResource'>"+obj.disciplines+"</p>"

                +"</div>"

            +"</div>"

            +"<div class='row' id='rowInfoResource'>"

                +"<div class='col-12'>"

                    +"<div class='contenitoreRating'>"

                        +ratingResource


                    +"</div>"

                    
                +"</div>"

            +"</div>"

        +"</div>"

        +"<div class='col-2' id='colResourceImage'>"

            +"<input  class='form-check-input' type='checkbox' id='flexCheckDefault' name='"+obj.title+"' idresource='"+id+"' onclick='showModalSections(this)'>"

        +"</div>"


        +"</div>";

        $("#elencoRisorse").append(currentResource);


    };

    hideLoading();

}

//FUNZIONE CHE APRE IL MODAL IN BASE ALLA RISORSA SELEZIONATA
function selectResource(image){
    let id=image.getAttribute("id");
    let name=image.getAttribute("name");
    
    sessionStorage.setItem("selectedResource",id);

    showModal(name);

}

//FUNZIONE CHE SCRIVE NELL'ANTEPRIMA DELLA RISORSA LE SUE CARATTERISTICHE
function showModal(resource){



    var resourceID;

    $("h5[class='modal-title']").empty();
    $(".descrizioneRisorsa").empty();

    $("#languageResource").empty();
    $("#difficultyResource").empty();
    $("#durationResource").empty();
    $("#formatResource").empty();
    $("#typeResource").empty();
    $("#ageResource").empty();

    $("#languageResourceSmartphone").empty();
    $("#difficultyResourceSmartphone").empty();
    $("#durationResourceSmartphone").empty();
    $("#formatResourceSmartphone").empty();
    $("#typeResourceSmartphone").empty();
    $("#ageResourceSmartphone").empty();


    $("#languageAccount").empty();
    $("#difficultyAccount").empty();
    $("#durationAccount").empty();
    $("#formatAccount").empty();
    $("#typeAccount").empty();
    $("#ageAccount").empty();

    $("#languageAccountSmartphone").empty();
    $("#difficultyAccountSmartphone").empty();
    $("#durationAccountSmartphone").empty();
    $("#formatAccountSmartphone").empty();
    $("#typeAccountSmartphone").empty();
    $("#ageAccountSmartphone").empty();


    document.getElementById("immagineAnteprima").setAttribute("src","");
    document.getElementById("immagineAnteprimaSmartphone").setAttribute("src","");
    $("#ratingRisorsa").empty();
    $("#stelleRisorsa").empty();
    document.getElementById("btnAggiungiRisorsa").setAttribute("name","");
    $("#btnAggiungiRisorsa").empty();
    $(".bar-1").css("width","0%");
    $(".bar-2").css("width","0%");
    $(".bar-3").css("width","0%");
    $(".bar-4").css("width","0%");
    $(".bar-5").css("width","0%");
    $("#valutazioneRisorsa").empty();
    $("#valutazioneStelleRisorsa").empty();
    $("#regolaRisorsa").empty();


    document.getElementById("btnAggiungiRisorsa").setAttribute("name",resource);


    $("h5[class='modal-title']").append(resource);
    

    
    for(i=0;i<recommendedResources.length;i++){
        var obj=recommendedResources[i];
        

        if(obj.title==resource){
            resourceID=obj.id;
            let description=obj.description;
            let language=obj.language;
            let difficulty=obj.difficulty;
            let duration=obj.duration;
            let format=obj.format;
            let type=obj.type;
            let minAge=obj.min_age;
            let maxAge=obj.max_age;
            let rating=obj.average_rating;
            let image;
            let utility=(Math.floor(obj.utility*100));
            let preferences=obj.preferences;

            preferences=preferences.slice(1);
            preferences=preferences.slice(0,-1);


            if(obj.resource_image=="unknown"){
                image="img/resource.png";
            }else{
                image=obj.resource_image;
            }


            document.getElementById("immagineAnteprima").setAttribute("src",image);
            document.getElementById("immagineAnteprimaSmartphone").setAttribute("src",image);
            
            $("#languageResource").append(language);

            
            if(difficulty=="Medio Bassa"){
                $("#difficultyResource").append("M.Bassa");
            }else if(difficulty=="Medio Alta"){
                $("#difficultyResource").append("M. Alta");
            }else{
                $("#difficultyResource").append(difficulty);
            }


            $("#durationResource").append(duration);

            if(format=="Executable Program"){
                $("#formatResource").append("Executable");
            }else{
                $("#formatResource").append(format);
            }

            if(type=="Reference Material"){
                $("#typeResource").append("R.Material");
            }else if(type=="Drill and Practice"){
                $("#typeResource").append("Practice");
            }else if(type=="Open (Access) Textbook"){
                $("#typeResource").append("Textbook");
            }else if(type=="Learning Object Repository"){
                $("#typeResource").append("Learning object");
            }else if(type=="Open (Access) Journal-Article"){
                $("#typeResource").append("Journal-Article");
            }else if(type=="Workshop and Training Material"){
                $("#typeResource").append("Training Material");
            }else{
                $("#typeResource").append(type);
            }

            $("#ageResource").append(minAge+"-"+maxAge);


            $("#languageResourceSmartphone").append(language);

            if(difficulty=="Medio Bassa"){
                $("#difficultyResourceSmartphone").append("M.Bassa");
            }else if(difficulty=="Medio Alta"){
                $("#difficultyResourceSmartphone").append("M. Alta");
            }else{
                $("#difficultyResourceSmartphone").append(difficulty);
            }

            $("#durationResourceSmartphone").append(duration);

            if(format=="Executable Program"){
                $("#formatResourceSmartphone").append("Executable");
            }else{
                $("#formatResourceSmartphone").append(format);
            }

            if(type=="Reference Material"){
                $("#typeResourceSmartphone").append("R.Material");
            }else if(type=="Drill and Practice"){
                $("#typeResourceSmartphone").append("Practice");
            }else if(type=="Open (Access) Textbook"){
                $("#typeResourceSmartphone").append("Textbook");
            }else if(type=="Learning Object Repository"){
                $("#typeResourceSmartphone").append("Learning object");
            }else if(type=="Open (Access) Journal-Article"){
                $("#typeResourceSmartphone").append("Journal-Article");
            }else if(type=="Workshop and Training Material"){
                $("#typeResourceSmartphone").append("Training Material");
            }else{
                $("#typeResourceSmartphone").append(type);
            }

            
            $("#ageResourceSmartphone").append(minAge+"-"+maxAge);


            let lingue=JSON.parse(sessionStorage.getItem("languages"));
            let difficoltà=JSON.parse(sessionStorage.getItem("difficulties"));
            let durate=JSON.parse(sessionStorage.getItem("duration"));
            let formati=JSON.parse(sessionStorage.getItem("formats"));
            let tipi=JSON.parse(sessionStorage.getItem("types"));
            let minEtà=JSON.parse(sessionStorage.getItem("minimumage"));
            let maxEtà=JSON.parse(sessionStorage.getItem("maximumage"));

            var difficoltàSemplice;

            if(difficoltà.length>0){
                if(difficoltà[0]=="Medio Alta"){
                    difficoltàSemplice="M.Alta";
                }else if(difficoltà[0]=="Medio Bassa"){
                    difficoltàSemplice="M.Bassa";
                }else{
                    difficoltàSemplice=difficoltà[0];
                }
            }else{
                difficoltàSemplice="nullo";
            }


            if(lingue.length>0){
                $("#languageAccount").append(lingue[0]);
                $("#languageAccountSmartphone").append(lingue[0]);
            }else{
                $("#languageAccount").append("nullo");
                $("#languageAccountSmartphone").append("nullo");
            }

            $("#difficultyAccount").append(difficoltàSemplice);
            $("#difficultyAccountSmartphone").append(difficoltàSemplice);

            if(durate.length>0){
                $("#durationAccount").append(durate[0]);
                $("#durationAccountSmartphone").append(durate[0]);
            }else{
                $("#durationAccount").append("nullo");
                $("#durationAccountSmartphone").append("nullo");
            }

            if(formati.length>0){
                $("#formatAccount").append(formati[0]);
                $("#formatAccountSmartphone").append(formati[0]);
            }else{
                $("#formatAccount").append("nullo");
                $("#formatAccountSmartphone").append("nullo");
            }

            if(tipi.length>0){
                $("#typeAccount").append(tipi[0]);
                $("#typeAccountSmartphone").append(tipi[0]);
            }else{
                $("#typeAccount").append("nullo");
                $("#typeAccountSmartphone").append("nullo");
            }

            if(minEtà[0]!=null || maxEtà[0]!=null){
                $("#ageAccount").append(minEtà[0]+"-"+maxEtà[0]);
                $("#ageAccountSmartphone").append(minEtà[0]+"-"+maxEtà[0]);
            }else{
                $("#ageAccount").append("nullo");
                $("#ageAccountSmartphone").append("nullo");
            }



            $(".descrizioneRisorsa").append(description);

            $("#valutazioneRisorsa").append(rating+".0");

            for(i=0;i<rating;i++){
                let star="<span class='fa fa-star checked'></span>";
                $("#valutazioneStelleRisorsa").append(star);
            }

            for(k=0;k<(5-rating);k++){
                let notstar="<span class='fa fa-star'></span>";
                $("#valutazioneStelleRisorsa").append(notstar);
            }
            


            var conteggioVoti=[0,0,0,0,0];
            var numeroValutazioni=0;

            for(var id in ratingResources){

                if(id==resourceID){

                    if(ratingResources[id].length>0){

                        for(var voto in ratingResources[id]){
    
                            for(var votoUtente in ratingResources[id][voto]){
                                
                                switch(votoUtente){
        
                                    case "1":
                                        conteggioVoti[0]=parseInt(ratingResources[id][voto][votoUtente]);
                                        numeroValutazioni+=ratingResources[id][voto][votoUtente];
                                        break;
                                        
                                    case "2":
                                        conteggioVoti[1]=parseInt(ratingResources[id][voto][votoUtente]);
                                        numeroValutazioni+=ratingResources[id][voto][votoUtente];
                                        break;
                                        
                                    case "3":
                                        conteggioVoti[2]=parseInt(ratingResources[id][voto][votoUtente]);
                                        numeroValutazioni+=ratingResources[id][voto][votoUtente];
                                        break;
                                        
                                    case "4":
                                        conteggioVoti[3]=parseInt(ratingResources[id][voto][votoUtente]);
                                        numeroValutazioni+=ratingResources[id][voto][votoUtente];
                                        break;
                                        
                                    case "5":
                                        conteggioVoti[4]=parseInt(ratingResources[id][voto][votoUtente]);
                                        numeroValutazioni+=ratingResources[id][voto][votoUtente];
                                        break;
                                        
    
                                }

                            }
        
                        }
    
                    }

                }

            }

            utilityResource=utility;
            var options;

            if(utility<30){

                colorProgress="#c70e0e";

                options = {
                    // item number you want to update
                    index: 1,
                    // update props
                    percent: utility,
                    colorSlice: "#c70e0e",
                    fontColor: "#c70e0e",
                    size: "120"
                };

            }else if(utility>=30 && utility<50){

                colorProgress="#ff8d03";

                 options = {
                    // item number you want to update
                    index: 1,
                    // update props
                    percent: utility,
                    colorSlice: "#ff8d03",
                    fontColor: "#ff8d03",
                    size: "120"
                };

            }else if(utility>=50 && utility<80){

                colorProgress="#ffc803";

                 options = {
                    // item number you want to update
                    index: 1,
                    // update props
                    percent: utility,
                    colorSlice: "#ffc803",
                    fontColor: "#ffc803",
                    size: "120"
                };

            }else{

                colorProgress="#58b314";

                 options = {
                    // item number you want to update
                    index: 1,
                    // update props
                    percent: utility,
                    colorSlice: "#58b314",
                    fontColor: "#58b314",
                    size: "120"
                };

            }

            setTimeout(() => {
                circle.animationTo(options);
            }, 300);

           



            /*var explanation="Ti è stata suggerita questa risorsa perchè rispecchia i metadati che sono stati selezionati:";

            for(i=0;i<preferences.length;i++){
                explanation.concat("\n",preferences[i]);
            }

            $("#regolaRisorsa").append(explanation);

            console.log(preferences.length);*/


            

            for(i=0;i<conteggioVoti.length;i++){

                var ratingValue=((conteggioVoti[i]/numeroValutazioni)*100)+"%";

                $(".bar-"+(i+1)).css("width",ratingValue);

            }


            if($("input[name='"+obj.title+"']").is(":checked")){
                $("#btnAggiungiRisorsa").append("Rimuovi");
            }else{
                $("#btnAggiungiRisorsa").append("Aggiungi");
            }


            break;

        }
    }

}

//FUNZIONE CHE MEMORIZZA LE RISORSE SELEZIONATE DALL'UTENTE NELLO STEP 3
function memorizzaRisorseSelezionate(){

    var risorseSelezionate=[];
    var idResources=[];

    for(i=0;i<recommendedResources.length;i++){

        var obj=recommendedResources[i];
        var title=obj.title;
        var id=obj.id;

        if($("input[name='"+title+"']").is(':checked')){
            risorseSelezionate.push(title);
            idResources.push(id);
        }


    }

    sessionStorage.setItem("risorseSelezionate",JSON.stringify(risorseSelezionate));
    sessionStorage.setItem("idResources",JSON.stringify(idResources));
    sessionStorage.setItem("moduleResources",JSON.stringify(moduleResource));

}

//FUNZIONE CHE MEMORIZZA LE INFORMAZIONI PRINCIPALI DEL CORSO CHE L'UTENTE HA SCELTO NELLA PRIMA SCHERMATA
function memorizzaInfo(){

    let nomeCorso=$("input[name='courseName']").val();
    let categoriaCorso=$("#selectCategoria").val();

    let nomiSezioni=[];
    let numeroSezioni;

    sessionStorage.setItem("courseName",nomeCorso);
    sessionStorage.setItem("courseCategory",categoriaCorso);
    
    if($("#selectSottocategoria").val()!="Sottocategoria"){
        let sottocategoriaCorso=$("#selectSottocategoria").val();
        sessionStorage.setItem("courseSubcategory",sottocategoriaCorso);
    }

    if($("input[name='checkboxSections']").is(':checked')){
        numeroSezioni=$("#selectNumeroSezioni").val();
        sessionStorage.setItem("courseSections",numeroSezioni);
    }else{
        numeroSezioni=1;
    }

    for(i=1;i<=numeroSezioni;i++){
        let nome=$("input[name='modulo"+i+"']").val();

        if(nome==""){
            nomiSezioni.push("Modulo "+i);
        }else{
            nomiSezioni.push(nome);
        }

        
    }

    sessionStorage.setItem("nameSections",JSON.stringify(nomiSezioni));

}

//FUNZIONE CHE NELLO STEP4 STAMPA TUTTE LE INFORMAZIONI E LE PREFERENZE DEL CORSO SCELTE DALL'UTENTE NELLA FASI PRECEDENTI
function stampaRiepilogo(){

    let nomeCorso=sessionStorage.getItem("courseName");
    let categoriaCorso=sessionStorage.getItem("courseCategory");
    let moduleResources=JSON.parse(sessionStorage.getItem("moduleResources"));
    $("#boldCourseName").empty();
    $("#boldCourseCategory").empty();
    $("#sottoCategoriaCorso").css("display","none");
    $("#numeroSezioniCorso").css("display","none");

    $("#boldCourseName").append(nomeCorso);
    $("#boldCourseCategory").append(categoriaCorso);

    if(sessionStorage.getItem("courseSubcategory")){
        let sottocategoriaCorso=sessionStorage.getItem("courseSubcategory");
        $("#sottoCategoriaCorso").css("display","");
        $("#boldCourseSubcategory").append(sottocategoriaCorso);
    }

    if(sessionStorage.getItem("courseSections")){
        let numeroSezioni=sessionStorage.getItem("courseSections");
        $("#numeroSezioniCorso").css("display","");
        $("#boldCourseSectionNumbers").append(numeroSezioni);
    }


    let risorseSelezionate=JSON.parse(sessionStorage.getItem("risorseSelezionate"));

    $("#risorseAggiunteRiepilogo").remove();

    for(i=0;i<moduleResources.length;i++){


        if(moduleResources[i].nomirisorse.length>0){    

            if(i!=0){

                let divisoria="<div class='row' style='height:1vh;background-color:white;margin-top:1vh;'>"
                +"<div class='col-12'>"
                +"</div>"
                +"</div>";
    
                $("#risorseAggiunte").append(divisoria);

            }
            
           

            let nomeSezione=moduleResources[i].modulo;

            let cardNomeSezione="<div class='row' style='text-align:center;margin-top:2vh;' id='risorseAggiunteRiepilogo'>"
            +"<div class='col-12'>"
                +"<span style='font-family:Avenir Light;font-weight:600;'>"+nomeSezione+"</span>"
            +"</div>"
            +"</div>";
    
            $("#risorseAggiunte").append(cardNomeSezione);

    
            for(j=0;j<moduleResources[i].nomirisorse.length;j++){
                let nomeRisorsa=moduleResources[i].nomirisorse[j];
    
                let cardNomeRisorsa="<div class='row' style='text-align:center;margin-top:2vh;' id='risorseAggiunteRiepilogo'>"
                +"<div class='col-12'>"
                    +"<span style='font-family:'Avenir Light';'><i>"+nomeRisorsa+"</i></span>"
                +"</div>"
                +"</div>";
    
                $("#risorseAggiunte").append(cardNomeRisorsa);
    
            }

            

        }



    }
    

}

function showGreen(){

    document.getElementById("ellisseConferma").setAttribute("fill","#5fad55");
    document.querySelector("path[name='tracciatoConferma']").setAttribute("fill","white");

}

function showTransparent(){
    document.getElementById("ellisseConferma").setAttribute("fill","none");
    document.querySelector("path[name='tracciatoConferma']").setAttribute("fill","#5fad55");
    
}

function memorizzaValutazioni(risorse){

    var jsondata={'arrayRisorse': risorse};

    $.ajax({
        type: "POST",
        url: "ratings_json.php",
        data: jsondata,
        aysnc:false,
        success: function(result){

            ratingResources=jQuery.parseJSON(result);

            

        },
        error: function (error) {
            console.error(error);

        }
    });

}


//FUNZIONE CHE PASSA AL PHP FINALE DELLA CREAZIONE DEL CORSO  TUTTE LE INFORMAZIONI RELATIVE AL CORSO E LE RISORSE AGGIUNTE
function memorizzaInformazioniCorso(){

    let courseName;
    let courseCategory;
    let courseSubcategory;
    let courseSections;
    let idResources;
    let titleResources;


    if(sessionStorage.getItem("courseName")){
        courseName=sessionStorage.getItem("courseName");
    }

    if(sessionStorage.getItem("courseCategory")){
        courseCategory=sessionStorage.getItem("courseCategory");
    }

    if(sessionStorage.getItem("courseSubcategory")){
        courseSubcategory=sessionStorage.getItem("courseSubcategory");
    }else{
        courseSubcategory=null;
    }

    if(sessionStorage.getItem("courseSections")){
        courseSections=sessionStorage.getItem("courseSections");
    }else{
        courseSections=1;
    }

    if(sessionStorage.getItem("idResources")){
        idResources=JSON.parse(sessionStorage.getItem("idResources"));
    }else{
        idResources=[];
    }

    if(sessionStorage.getItem("risorseSelezionate")){
        titleResources=JSON.parse(sessionStorage.getItem("risorseSelezionate"));
    }else{
        titleResources=[];
    }

    let moduleResources=JSON.parse(sessionStorage.getItem("moduleResources"));

    var jsondata={
        'courseName': courseName,
        'courseCategory': courseCategory,
        'courseSubcategory': courseSubcategory,
        'courseSections': courseSections,
        'idResources': idResources,
        'titleResources':titleResources,
        'moduleResources':moduleResources
    };


    $.ajax({
        type: "POST",
        url: "crea_corso.php",
        data: jsondata,
        aysnc:false,
        success: function(result){

            console.log("id corso: "+result);
        },
        error: function (error) {
            console.error(error);

        }
    });

}

function stampaNomeCorso(){

    $("#confirmCourseName").empty();

    let nomeCorso=sessionStorage.getItem("courseName");
    $("#confirmCourseName").append(nomeCorso);

}

function showSectionsModal(){

    $("#sectionsContent").empty();

    let numeroSezioni;

    if($("input[name='checkboxSections']").is(':checked')){
        numeroSezioni=$("#selectNumeroSezioni").val();
    }else{
        numeroSezioni=1;
    }


    for(i=1;i<=numeroSezioni;i++){

        let modulo=	"<div class='row justify-content-center'>"
        +"<div class='col-auto'>"
            +"<input type='text' class='form-control' id='inputModule' placeholder='Modulo "+i+"' name='modulo"+i+"' style='width:30vh;'>"
        +"</div>"
        +"</div>";

        $("#sectionsContent").append(modulo);

    }



    $("#btnShowSectionsModal").click();
}

function saveNameSections(){
    let nomiSezioni=JSON.parse(sessionStorage.getItem("nameSections"));
    nameSections=nomiSezioni;
    
    for(i=0;i<nameSections.length;i++){
        let nome=nameSections[i];
        let modulo={"modulo":nome,"idrisorse":[],"nomirisorse":[]};
        moduleResource.push(modulo);
    }

    console.log(moduleResource);

}

function showModalSections(input){

    
    //STO AGGIUNGENDO LA RISORSA
    if(input.checked){

        caricaSezioni();

        $("#btnShowModalSections").click();

        selectedResource=input.getAttribute("idresource");
        nameSelectedResource=input.getAttribute("name");
        
        
    }else{
        //STO RIMUOVENDO LA RISORSA
        for(i=0;i<moduleResource.length;i++){
            let currentModule=moduleResource[i];

            for(j=0;j<currentModule.idrisorse.length;j++){
                if(currentModule.idrisorse[j]==input.getAttribute("idresource")){
                    currentModule.idrisorse.splice(j,1);
                    currentModule.nomirisorse.splice(j,1);
                }
            }

        }



    }

    

}

//CARICA NEL MODAL I MODULI CREATI
function caricaSezioni(){
    $("#moduleContent").empty();

    for(i=0;i<nameSections.length;i++){

        let nome=nameSections[i];

        let card="<div class='row justify-content-center' id='rowSeparaRisorse' style='margin-top:1vh' name='"+nome+"' selezionato='false' onclick='evidenziaModulo(this)'>"
        +"<div class='col-auto' id='colSeparaRisorse'>"
            +"<span style='float:left;'>"+nome+"</span>"
            +"<span class='material-icons' name='"+nome+"' id='confirmResourceSection' style='display:none'>check</span>"
        +"</div>"
        +"</div>";

        $("#moduleContent").append(card);

    }

}

//PERMETTE DI EVIDENZIARE UN MODULO QUANDO VIENE SELEZIONATO
function evidenziaModulo(modulo){

    
    let nome=modulo.getAttribute("name")
    let selezionato=modulo.getAttribute("selezionato");

    if(selezionato=="false"){
        modulo.setAttribute("selezionato","true");
        modulo.style.backgroundColor="#5fad55";
        modulo.style.color="white";
        $("span[name='"+nome+"']").css("display","");

    }else{
        modulo.setAttribute("selezionato","false");
        modulo.style.backgroundColor="#f6f6f6";
        modulo.style.color="black";
        $("span[name='"+nome+"']").css("display","none");
    }

}

//VIENE VISUALIZZATO UN ALERT QUANDO SI TENTA DI AGGIUNGERE UNA RISORSA SENZA SELEZIONARE UN MODULO
function erroreModulo(){

    Swal.fire({
        icon: 'error',
        title: 'Impossibile procedere',
        text: 'Seleziona almeno un modulo',
        confirmButtonColor: '#dc3545',
    })

}

//FUNZIONE CHE PERMETTE DI ESSERE REINDIRIZZATI AL CORSO
function vaiAlCorso(){

    var jsondata={
        'typeRequest': 'findCourseId'
    };


    $.ajax({
        type: "POST",
        url: "trova_corso.php",
        data: jsondata,
        success: function(idcorso){

            window.location.href='../course/view.php?id='+idcorso;
        },
        error: function (error) {
            console.error(error);

        }
    });

}

//NELLO STEP1 DOPO CHE SONO STATE CARICATE LE DISCIPLINE VENGONO MEMORIZZATE GLOBALMENTE
function saveDisciplines(discipline){
    disciplines=discipline;
}