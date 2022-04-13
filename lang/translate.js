
function handle_translation(obj, i, arg_en, arg_it, current_lang) 
{
	switch (current_lang) {
    	case "en":
    		translation = arg_en;
        	break;
    	case "it":
        	translation = arg_it;
        	break;
        default:
       		translation = arg_it;
	}
	
	x = obj
		
	x[i].innerText= translation;    // Change the content
	
	return translation;
}



function convert(obj, i, string, current_lang)
{    console.log(string);
switch (string) {    	
		
		case 'Il progetto WhoTeach mira a creare una piattaforma cloud sulle repository di apprendimento attuali (MOOC, open source o sottoposti a pagamento), che estrapola e integra i loro contenuti in base alle esigenze dell\'utente, a specifici criteri di qualità  e alle politiche di condivisione e autorizzazione. La piattaforma è concepita per utenti esigenti, ossia per persone che sono solite imparare ed, eventualmente, insegnare, che cercano contenuti didattici validati e ben organizzati per la propria crescita culturale o per l\'insegnamento.':        	
			string = handle_translation(obj, i, "WhoTeach aims to create a cloudplatform. It operates on current knowledge repositories, extrapolatingand integrating their contents according to customers' preferences, quality criteria and sharing policies. Whoteach has been designed for demanding users,ready to learn and teach, seeking for validated didactical content, already properly organised for their own culture's growth and training.", "Il progetto WhoTeach mira a creare una piattaforma cloud sulle repository di apprendimento attuali (MOOC, open source o sottoposti a pagamento), che estrapola e integra i loro contenuti in base alle esigenze dell'utente, a specifici criteri di qualità  e alle politiche di condivisione e autorizzazione. La piattaforma à¨ concepita per utenti esigenti, ossia per persone che sono solite imparare ed, eventualmente, insegnare, che cercano contenuti didattici validati e ben organizzati per la propria crescita culturale o per l'insegnamento.", current_lang)
        	break;
    	
		//case 'Il progetto WhoTeach mira a creare una piattaforma cloud sulle repository di apprendimento attuali (MOOC, open source o sottoposti a pagamento), che estrapola e integra i loro contenuti in base alle esigenze dell'utente, a specifici criteri di qualità  e alle politiche di condivisione e autorizzazione. La piattaforma à¨ concepita per utenti esigenti, ossia per persone che sono solite imparare ed, eventualmente, insegnare, che cercano contenuti didattici validati e ben organizzati per la propria crescita culturale o per l'insegnamento.':
		//	string = handle_translation(obj, i, "The WhoTeach project aims to create a cloud platform on the current learning repository (MOOC, open source or subject to payment), which extrapolates and integrates their content based on user needs, specific quality criteria and sharing policies and authorization. The platform is designed for demanding users, people who are used to learn or teach, who are looking for validated and well-organized educational content for their own cultural growth or for teaching.", current_lang)
		//	break;
		
		case 'Offriamo corsi per ogni esigenza':
        	string = handle_translation(obj, i, "We offer courses for every need", "Offiramo corsi per ogni esigenza", current_lang);
        	break;
		case 'Raggiungici':
        	string = handle_translation(obj, i, "Join Us", "Raggiungici", current_lang);
        	break;
		case 'Crea un corso':
        	string = handle_translation(obj, i, "Create a course", "Crea un corso", current_lang);
        	break;
		case 'Per poter creare un corso bisogna avere il ruolo di Contributor, per ottenerlo bisogna contattare l\'Admin User tramite il modulo di messaggistica istantanea presente nella piattaforma oppure inviando una mail a ':
        	string = handle_translation(obj, i, "To create one you need the Contributor role. To obtain it, you need to contact Admin User via our platform chat or sending an email to: ", "Per poter creare un corso bisogna avere il ruolo di Contributor, per ottenerlo bisogna contattare l'Admin User tramite il modulo di messaggistica istantanea presente nella piattaforma oppure inviando una mail a ", current_lang);
        	break;
		case 'Revisiona un corso':
        	string = handle_translation(obj, i, "How do I edit a course?", "Revisiona un corso", current_lang);
        	break;
		case 'Per poter revisionare un corso bisogna avere il ruolo di Master o Expert per la categoria del corso stesso. Per ottenere il cambio di ruolo bisogna contattare l’Admin User tramite il modulo di messaggistica istantanea presente nella piattaforma oppure inviando una mail a ':
        	string = handle_translation(obj, i, "To do it, you need Master or Expert role for the course category. To obtain it, just contact Admin User via our platform chat or sending an email to:", "Per poter revisionare un corso bisogna avere il ruolo di Master o Expert per la categoria del corso stesso. Per ottenere il cambio di ruolo bisogna contattare l\'Admin User tramite il modulo di messaggistica istantanea presente nella piattaforma oppure inviando una mail a ", current_lang);
        	break;
		case 'Altre domande':
        	string = handle_translation(obj, i, "Other question", "Altre domande", current_lang);
        	break;
		case 'Per altre informazioni contattare ':
        	string = handle_translation(obj, i, "For more info, write to ", "Per altre informazioni contattare ", current_lang);
        	break;
		case 'Abilità Informatiche':
		case 'Abilità informatiche':
		case 'abilità Informatiche':
		case 'Computer Science skills':
        	string = handle_translation(obj, i, "Computer Science skills", "Abilità  Informatiche", current_lang);
        	break;
		case 'Competenze in economia':
		case 'Competenze in Economia':
		case 'competenze in economia':
		case 'Business Capabilities':
        	string = handle_translation(obj, i, "Business Capabilities", "Competenze in economia", current_lang);
        	break;
		case 'Abilità comunicative':
		case 'Abilità Comunicative':
		case 'abilità comunicative':
		case 'Communication skills':
        	string = handle_translation(obj, i, "Communication skills", "Abilità  comunicative", current_lang);
        	break;
		case 'Visione imprenditoriale':
		case 'visione imprenditoriale':
		case 'Entrepreneurial vision':
        	string = handle_translation(obj, i, "Entrepreneurial vision", "Visione imprenditoriale", current_lang);
        	break;
		case 'Sviluppo personale':
		case 'Sviluppo Personale':
		case 'sviluppo personale':
		case 'Self-Growth':
        	string = handle_translation(obj, i, "Self-Growth", "Sviluppo personale", current_lang);
        	break;
		case 'Scopri di più':
        	string = handle_translation(obj, i, "See more", "Scopri di più", current_lang);
        	break;
		case 'Courses':
        	string = handle_translation(obj, i, "Courses", "Corsi", current_lang);
        	break;
		case 'Revisions':
        	string = handle_translation(obj, i, "Revisions", "Revisioni", current_lang);
        	break;
		case 'Domande recenti:':
        	string = handle_translation(obj, i, "Recent questions:", "Domande recenti:", current_lang);
        	break;
		case 'Who we are':
		case 'Chi siamo':
        	string = handle_translation(obj, i, "Who we are", "Chi siamo", current_lang);
        	break;
		case 'sviluppa tecnologie ICT innovative e algoritmi per collegare persone e oggetti attraverso':
		case 'develops innovative ICT systems and algorithms connecting objects through smart':
        	string = handle_translation(obj, i, "develops innovative ICT systems and algorithms connecting objects through smart", "sviluppa tecnologie ICT innovative e algoritmi per collegare persone e oggetti attraverso", current_lang);
        	break;
		case 'intelligenti. In particolare, il Social Intelligent Learning Management System (WhoTeach) ha lo scopo di favorire lo sviluppo di competenze di professionisti e manager aggregando e diffondendo conoscenza creata da esperti.':
		
		case "Whoteach is a Learning Management System that aims to foster the development of employees capabilities,aggregating and diffusing knowledge from experts.":
        	string = handle_translation(obj, i, "Whoteach is a Learning Management System that aims to foster the development of employees' capabilities,aggregating and diffusing knowledge from experts.", "intelligenti. In particolare, il Social Intelligent Learning Management System (WhoTeach) ha lo scopo di favorire lo sviluppo di competenze di professionisti e manager aggregando e diffondendo conoscenza creata da esperti.", current_lang);
        	break;
		
		case "Accedi":
        	string = handle_translation(obj, i, "Join", "Accedi", current_lang);
        	break;

		case "Sedi operative:":
        	string = handle_translation(obj, i, "Operational headquarters:", "Sedi operative:", current_lang);
        	break;

		case "Sede legale:":
        	string = handle_translation(obj, i, "Registered office:", "Sede legale:", current_lang);
        	break;

		case "Tutti i corsi":
        	string = handle_translation(obj, i, "See all curses", "Tutti i corsi", current_lang);
        	break;

		case 'Non sono consentiti spazi nel username':
        	string = handle_translation(obj, i, "No spaces are allowed in the username", "Non sono consentiti spazi nel username", current_lang);
        	break;
		
		case 'Abilità Informatiche':
        	string = handle_translation(obj, i, "Abilità Informatiche", "Computer Skills", current_lang);
        	break;
		
		case 'Sviluppo personale':
        	string = handle_translation(obj, i, "Personal Development", "Sviluppo personale", current_lang);
        	break;

			
	}
	
	return string;
}


window.addEventListener('load', function(){
	y = document.getElementsByClassName("translate");
	lang = document.getElementsByTagName("html")[0].lang;
	for(var i = 0; i < y.length; i++){
			convert(y, i, y[i].textContent, lang);  // Change the content
		}
	z = document.getElementsByTagName("a");
	for(i = 0; i < z.length; i++){
			convert(z, i, z[i].textContent, lang);  // Change the content
		}
});
	
	
	