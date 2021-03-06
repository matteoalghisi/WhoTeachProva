<?php

function handle_translation($arg_en, $arg_it, $arg_tr, $arg_bg) //s_t_mod Chiara: era già tutto ok
{
	$current_lang = current_language();
	switch ($current_lang) {
    	case "en":
    		$translation = $arg_en;
        	break;
    	case "it":
        	$translation = $arg_it;
        	break;
    	case "tr":
        	$translation = $arg_tr;
        	break;
        case "bg":
        	$translation = $arg_bg;
        	break;
        default:
       		$translation = $arg_en;
	}
	return $translation;
}

function convert_metadata($metadata) //s_t_mod Chiara: aggiunte nuove traduzioni
{
    switch ($metadata) {
    	case 'language':
        	$metadata = handle_translation("Language", "Lingua", "Dil", "език");
        	break;
    	case 'keywords':
        	$metadata = handle_translation("Keywords", "Keywords", "Anahtar kelime", "ключови думи");
        	break;
    	case 'format':
        	$metadata = handle_translation("Format", "Formato", "Format", "формат");
        	break;
    	case 'resourcetype':
        	$metadata = handle_translation("Learning Resource Type", "Tipo di Risorsa", "Kaynak türü", "Тип на ресурса");
        	break;
	case '(either contents or activities)':
        	$metadata = handle_translation("(either contents or activities)", "(sia contenuti che attività)", "(içerikleri veya faaliyetler ya)", "(или съдържание или дейности)");
        	break;
	case 'Contents':
        	$metadata = handle_translation("Contents", "Contenuti", "içindekiler", "съдържание");
        	break;
	case 'Activities':
        	$metadata = handle_translation("Activities", "Attività", "faaliyetler", "дейности");
        	break;
    	case 'min_age':
        	$metadata = handle_translation("Minimal Age", "Età Minima", "Asgari yaş", "Минимална възраст");
        	break;
	case 'max_age':
        	$metadata = handle_translation("Maximal Age", "Età Massima", "Maksimum yaş", "Максимална възраст");
        	break;
	case 'age_rule': //s_t_mod Hui
        	$metadata = handle_translation("The Minimal Age must be smaller than the Maximal Age", "L'Età Minima deve essere più piccola dell'Età Massima", "The Minimal Age must be smaller than the Maximal Age", "The Minimal Age must be smaller than the Maximal Age");
        	break;
    	case 'difficulty':
        	$metadata = handle_translation("Difficulty", "Difficoltà", "Zorluk derecesi", "сложност");
        	break;
    	case 'time':
        	$metadata = handle_translation("Typical Learning Time", "Tempo d'Apprendimento", "Süre", "време");
        	break;
    	case 'category':
        	$metadata = handle_translation("Category", "Categoria", "Kategori", "категория");
        	break;
    	case 's_req_skill':
        	$metadata = handle_translation("Background (defined at course level)", "Background (definito a livello del corso)", "Gerekli beceriler (ders düzeyinde belirlenen)", "Изисквани умения (дефинирани на ниво курс)");
        	break;
    	case 's_acq_skill':
        	$metadata = handle_translation("Acquired Skills (defined at course level)", "Abilità Acquisite (definite a livello del corso)", "kazanılan beceriler (ders düzeyinde belirlenen)", "Придобити умения (дефинирани на ниво курс)");
        	break;
    	case 'd_req_skill':
        	$metadata = handle_translation("Background (defined at module level)", "Background (definito a livello del modulo)", "Gerekli beceriler (modül düzeyinde belirlenen)", "Изисквани умения (дефинирани на ниво модул)");
        	break;
    	case 'd_acq_skill':
        	$metadata = handle_translation("Acquired Skills (defined at module level)", "Abilità Acquisite (definite a livello del modulo)", "Kazanılan beceriler (modül düzeyinde belirlenen)", "Придобити умения (дефинирани на ниво модул)");
        	break;
	case 'vpim':
		$metadata = handle_translation("View Previously Inserted Metadata", "Vedi Metadati Precedentemente Inseriti", "Daha önceden girilmiş olan metadatayı görün", "Покажи предишно въведени метаданни");
		break;
	case 'cm':
		$metadata = handle_translation("Check Metadata: ", "Verifica Metadati: ", "Metadatayı kontrol edin: ", "Провери метаданните: ");
		break;	
	case 'ks':
		$metadata = handle_translation('Keywords (separator ", ")', 'Keywords (separatore ", ")', 'Anahtar kelimeler (liste ayırıcı ", ")', 'ключови думи (сепаратор ", ")');
		break;
	case 'metadata':
		$metadata = handle_translation("Metadata", "Metadati", "Metadata", "Метаданни");
		break;	
	case 'missLang':
		$metadata = handle_translation("Missing Language", "Lingua Mancante", "Dil eksik", "Липсва Език");	
		break;
	case 'missFormat':
		$metadata = handle_translation("Missing Format", "Formato Mancante", "Format eksik", "Липсва Формат");
		break;
	case 'missLRT':	
		$metadata = handle_translation("Missing Learning Resource Type", "Tipo di Risorsa Mancante", "Öğrenme kaynaği türü eksik", "Липсва тип Учебен ресурс");	
		break;
	case 'missTLT':
		$metadata = handle_translation("Missing Typical Learning Time", "Tempo d'Apprendimento Mancante", "Tipik öğrenme süresi eksik", "Липсва времетраене");
		break;	
	
	//s_t_mod nuove traduzioni header -> non so la traduzione nelle altre due lingue
	case 'basic_metadata':
		$metadata = handle_translation("Basic Metadata", "Metadati di Base", "Basic Metadata", "Basic Medatada");
		break;
    
	case 'req_skills':
		$metadata = handle_translation("Required Skills Metadata", "Metadati Capacità Richieste", "Required Skills Metadata", "Required Skills Metadata");
		break;
    
	case 'acq_skills':
		$metadata = handle_translation("Acquired Skills Metadata", "Metadati Capacità Acquisite", "Acquired Skills Metadata", "Acquired Skills Metadata");
		break;
	case 'prec_metadata':
		$metadata = handle_translation("View Previously Inserted Metadata", "Visualizza Metadati Inseriti Precedentemente", "View Previously Inserted Metadata", "View Previously Inserted Metadata");
		break;
	case 'grade':
		$metadata = handle_translation("Grade:", "Livello:", "Grade:", "Grade:");
		break;
    }

    return $metadata;
}

function translate_skill($skill) //s_t_mod Chiara: Funzione non utilizzata --> per la traduzione delle skills viene usata la funzione translate_element
{
    switch ($skill) {
    
    	// Category 1: Entrepreneurial Vision
	case 'Proactivity':
		$skill = handle_translation('Proactivity', 'Proattività', 'Proaktiflik', 'проактивност');
		break;
	case 'Entrepreneurial behaviors and attitudes':
		$skill = handle_translation('Entrepreneurial behaviors and attitudes', 'Attività e attitudini imprenditoriali', 'Girişimsel davranış ve tutumlar', 'Предприемаческите нагласи и поведения');
		break;
	case 'Leadership':
		$skill = handle_translation('Leadership', 'Capacità di comando', 'Liderlik', 'Лидерски умения');
		break;
	case 'Self-evaluation':
		$skill = handle_translation('Self-evaluation', 'Autovalutazione', 'Öz-değerlendirme', 'Самооценката');
		break;
	case 'Self-organization':
		$skill = handle_translation('Self-organization', 'Auto-organizzazione', 'Öz-örgütlenme', 'Самоорганизация');
		break;
	case 'Innovative thinking':
		$skill = handle_translation('Innovative thinking', 'Pensiero innovativo', 'Yenilikçi düşünme', 'Иновативно мислене');
		break;
	case 'Creative thinking':
		$skill = handle_translation('Creative thinking', 'Pensiero creativo', 'Yaratıcı düşünme', 'Творческо мислене');
		break;
	case 'Opportunities Management':
		$skill = handle_translation('Opportunities Management', 'Gestione delle opportunità', 'Fırsat Yönetimi', 'Възможности за управление');
		break;
	case 'Ability to promote initiatives':
		$skill = handle_translation('Ability to promote initiatives', 'Capacità di promuovere iniziative', 'Girişimi teşvik edebilme', 'Насърчаване на инициативността');
		break;
	case 'Management Skills':
		$skill = handle_translation('Management Skills', 'Capacità Amministrative', 'Yönetim Becerileri', 'Управленски умения');
		break;
	case 'Risk Management':
		$skill = handle_translation('Risk Management', 'Gestione del Rischio', 'Risk Yönetimi', 'Управление на риска');
		break;	
		
	// Category 2: Personal Development
	case 'Interpersonal Relations':
		$skill = handle_translation('Interpersonal Relations', 'Relazioni Interpersonali', 'Kişilerarası İlişkiler', 'Междуличностни отношения');
		break;
	case 'Conflict Management':
		$skill = handle_translation('Conflict Management', 'Gestione dei Conflitti', 'Çatışma Yönetimi', 'Управление на конфликти');	
		break;
	case 'Team working':
		$skill = handle_translation('Team working', 'Lavoro di squadra', 'Takım çalışması', 'Работата в екип');	
		break;
	case 'Career Planning':
		$skill = handle_translation('Career Planning', 'Pianificazione della Carriera', 'Kariyer planlama', 'Кариерно планиране');	
		break;
	case 'Job Search Skills':	
		$skill = handle_translation('Job Search Skills', 'Capacità di Cercare Lavoro', 'İş Arama Becerileri', 'Умения за търсене на работа');
		break;
	case 'People Management':
		$skill = handle_translation('People Management', 'Gestione del Personale', 'İnsan Yönetimi', 'Управление на хора');
		break;
	case 'Training and Professional Development':
		$skill = handle_translation('Training and Professional Development', 'Formazione e Sviluppo Personali', 'Eğitim ve Mesleki Gelişim', 'Обучение и професионално развитие');
		break;
	case 'Motivation':
		$skill = handle_translation('Motivation', 'Motivazione', 'Motivasyon', 'Мотивиране');
		break;
	case 'People and Performance Evaluation Skills':
		$skill = handle_translation('People and Performance Evaluation Skills', 'Capacità di valutare Persone e Prestazioni', 'Kişi ve Performans Değerlendirme Becerileri', 'Умения за оценяване на хора и представяне');
		break;
	case 'Responsibility':
		$skill = handle_translation('Responsibility', 'Responsabilità', 'Sorumluluk', 'Отговорност');
		break;
	
	// Category 3: Communication Skills
	case 'Communications Basics':
		$skill = handle_translation('Communications Basics', 'Fondamenti di Comunicazione', 'İletişimin Temelleri', 'Основни умения за общуване');
		break;
	case 'Communication Ethics':
		$skill = handle_translation('Communication Ethics', 'Etica della Comunicazione', 'İletişim Etiği', 'Етика на общуването');
		break;
	case 'Information Management':
		$skill = handle_translation('Information Management', 'Gestione dell\'Informazione', 'Bilgi Yönetimi', 'Информационен мениджмънт');	
		break;
	case 'Data Management':
		$skill = handle_translation('Data Management', 'Gestione dei Dati', 'Veri Yönetimi', 'Управление на данни');
		break;
	case 'Information Technology Basics':
		$skill = handle_translation('Information Technology Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilgi Teknolojilerinin Temelleri', 'Основни ИТ умения');	
		break;
	case 'Product and Service Marketing':
		$skill = handle_translation('Product and Service Marketing', 'Marketing dei Prodotti e dei Servizi', 'Ürün ve Hizmet Pazarlama', 'Маркетинг на продукти и услуги');
		break;
	case 'Marketing Information Management':
		$skill = handle_translation('Marketing Information Management', 'Gestione delle Informazioni di Marketing', 'Pazarlama Bilgi Yönetimi', 'Маркетинг на информационен мениджмънт');
		break;
	case 'Strategic Marketing  Planning':
		$skill = handle_translation('Strategic Marketing Planning', 'Pianificazione Strategica di Mercato', 'Stratejik Pazarlama Planlaması', 'Стратегическо маркетинг планиране');
		break;
		
	// Categoria 4: Economic Skills
	case 'Business Basics':
		$skill = handle_translation('Business Basics', 'Nozioni Base di Business', 'Ticaretin Temelleri', 'Базисни бизнес умения');
		break;
	case 'Business Attitudes':	
		$skill = handle_translation('Business Attitudes', 'Mentalità da Business', 'Ticaret Tutumları', 'Бизнес нагласи');
		break;
	case 'Decision Making':
		$skill = handle_translation('Decision Making', 'Processo Decisionale', 'Karar Verme', 'Вземане на решение');	
		break;
	case 'Economic Culture':
		$skill = handle_translation('Economic Culture', 'Conoscenze di Economia', 'Ekonomik Kültür', 'Икономическа култура');	
		break;
	case 'Financial Basics':
		$skill = handle_translation('Financial Basics', 'Fondamenti di Finanza', 'Finansın Temelleri', 'Основи на финансите');
		break;
	case 'Treasury Management':
		$skill = handle_translation('Treasury Management', 'Gestione della Tesoreria', 'Hazine Yönetimi', 'Управление на финанси');	
		break;
	case 'Accounting':
		$skill = handle_translation('Accounting', 'Contabilità', 'Muhasebe', 'Счетоводство');
		break;
	case 'Enterprise Modeling':
		$skill = handle_translation('Enterprise Modeling', 'Modellazione dei Processi Aziendali', 'Kurumsal Modelleme', 'Моделиране на производство');
		break;
	case 'Distribution Channels Management':
		$skill = handle_translation('Distribution Channels Management', 'Gestione dei Canali di Distribuzione', 'Dağıtım Kanalları Yönetimi', 'Управление на каналите за дистрибуция');
		break;
	case 'Purchasing Management':
		$skill = handle_translation('Purchasing Management', 'Gestione degli Acquisti', 'Satınalma Yönetimi', 'Управление на покупателните способности');
		break;
	case 'Operations Management':
		$skill = handle_translation('Operations Management', 'Gestione delle Operazioni', 'Operasyon Yönetimi', 'Управление на операции и процеси');
		break;			 	
		
	// Category 5: Technical Skills
	case 'Computer Skills':
		$skill = handle_translation('Computer Skills', 'Abilità Informatiche', 'Bilgisayar Becerileri', 'Компютърни умения');
		break;	
	case 'IT Basics':
		$skill = handle_translation('IT Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilişimin Temelleri', 'Основни ИТ умения');
		break;
	case 'IT Applications Basics':
		$skill = handle_translation('IT Applications Basics', 'Applicazioni Informatiche di Base', 'Bilişim Uygulamalarının Temelleri', 'Основни умения за ИТ приложения');
		break;
	case 'Electronic System Tools Basics':
		$skill = handle_translation('Electronic System Tools Basics', 'Fondamenti di Strumenti Elettronici', 'Elektronik Sistem Araçlarının Temelleri', 'Основни умения за работа с инструментите на електонни системи');
		break;
	case 'Graphical editor':
		$skill = handle_translation('Graphical editor', 'SW di Grafica', 'Çizim SW', 'Графичен редактор');
		break;
	case 'Calculation SW':
		$skill = handle_translation('Calculation SW', 'SW di Calcolo', 'Hesaplama SW', 'Софтуер за изчисляван');
		break;
	case 'Project Management SW':
		$skill = handle_translation('Project Management SW', 'SW di Gestione di Progetto', 'Proje Yönetimi SW', 'Софтуер за управление на проекти');
		break;
	case 'Document Management SW':
		$skill = handle_translation('Document Management SW', 'SW di Gestione di Documenti', 'Belge Yönetimi SW', 'Софтуер за управление на документни потоци');
		break;
	case 'Planning and Control SW':
		$skill = handle_translation('Planning and Control SW', 'SW di Pianificazione e Controllo', 'Planlama ve Kontrol SW', 'Софтуер за планиране и контрол');
		break;
	case 'Simulation SW':
		$skill = handle_translation('Simulation SW', 'SW di Simulazione', 'Simulasyon SW', 'Симулационен софтуер');
		break;
	case 'Accounting SW':
		$skill = handle_translation('Accounting SW', 'SW di Contabilità', 'Muhasebe SW', 'Счетоводен софтуер');
		break;
	case 'Communication SW':
		$skill = handle_translation('Communication SW', 'SW di Comunicazione', 'İletişim SW', 'Комуникационен софтуер'); 
		break; 										
    }
    return $skill;
}

function translate_difficulty($difficulty) //s_t_mod Chiara: era già tutto ok
{
    switch ($difficulty) {
    	case 'Very easy':
    		$difficulty = handle_translation('Very easy', 'Molto facile', 'Çok Kolay', 'Много лесно');
    		break;
    	case 'Easy':
    		$difficulty = handle_translation('Easy', 'Facile', 'Kolay', 'Лесно');
    		break;
    	case 'Medium':
    		$difficulty = handle_translation('Medium', 'Media', 'Orta', 'Средно');
    		break;
    	case 'Difficult':
    		$difficulty = handle_translation('Difficult', 'Difficile', 'Zor', 'Трудно');
    		break;
    	case 'Very difficult':
    		$difficulty = handle_translation('Very difficult', 'Molto difficile', 'Çok Zor', 'Много лесно');
    		break;	
    }
    return $difficulty;			    	
}

function translate_language($language) ///s_t_mod Chiara: era già tutto ok
{
    switch ($language) {
	case 'English':
		$language = handle_translation('English', 'Inglese', 'İngilizce', 'Английски');
		break;
	case 'Italian':
		$language = handle_translation('Italian', 'Italiano', 'İtalyanca', 'Италиански');
		break;
	case 'Bulgarian':
		$language = handle_translation('Bulgarian', 'Bulgaro', 'Bulgarca', 'Български');
		break;
	case 'Turkish':
		$language = handle_translation('Turkish', 'Turco', 'Türkçe', 'Турски');
		break;			
    }
    return $language;
}

function translate_format($format) //s_t_mod Chiara: era già tutto ok
{
    switch ($format) {
	case 'Video':
		$format = handle_translation('Video', 'Video', 'Video', 'Видео');    
		break;
	case 'Images':
		$format = handle_translation('Images', 'Immagini', 'Görüntüler', 'снимки');    
		break;
	case 'Text':
		$format = handle_translation('Text', 'Testo', 'Metin', 'Текст');    
		break;
	case 'Audio':
		$format = handle_translation('Audio', 'Audio', 'Ses Kaydı', 'Аудио');    
		break;
	case 'Slide':
		$format = handle_translation('Slide', 'Diapositive', 'Slayt', 'пързалка');    
		break;						
    }
    return $format;
}

function translate_type($type) //s_t_mod Chiara: era già tutto ok
{
   switch (strtolower($type)) {
   	case 'exercise':
   		$type = handle_translation('Exercise', 'Esercizio', 'Alıştırma', 'Упражнение');
   		break;
   	case 'simulation':
   		$type = handle_translation('Simulation', 'Simulazione', 'Simulasyon', 'Симулация');
   		break;
   	case 'questionnaire':
   		$type = handle_translation('Questionnaire', 'Questionario', 'Въпросник', 'Questionnaire');
   		break;
   	case 'diagram':
   		$type = handle_translation('Diagram', 'Diagramma', 'Diyagram', 'Диаграма');
   		break;
   	case 'figure':
   		$type = handle_translation('Figure', 'Figura', 'Şekil', 'Фигура');
   		break;
   	case 'graph':
   		$type = handle_translation('Graph', 'Grafico', 'Grafik', 'Графика');
   		break;
   	case 'index':
   		$type = handle_translation('Index', 'Indice', 'İndeks', 'индекс');
   		break;
   	case 'slides':
   		$type = handle_translation('Slides', 'Diapositive', 'Slayt', 'Слайд');
   		break;
   	case 'table':
   		$type = handle_translation('Table', 'Tabella', 'Tablo', 'Таблица');
   		break;
   	case 'narrative text':
   		$type = handle_translation('Narrative text', 'Testo Narrativo', 'Düz metin', 'Oписание (Текст)');
   		break;
   	case 'exam':
   		$type = handle_translation('Exam', 'Esame', 'Sınav', 'Изпит');
   		break;
   	case 'experiment':
   		$type = handle_translation('Experiment', 'Esperimento', 'Deney', 'Експеримент');
   		break;
   	case 'problem statement':
   		$type = handle_translation('Problem statement', 'Definizione problema', 'Problem ifadesi', 'Описание на проблем');
   		break;
   	case 'self assessment':
   		$type = handle_translation('Self assessment', 'Autovalutazione', 'Özdeğerlendirme', 'Самооценка');
   		break;
   	case 'lecture':
   		$type = handle_translation('Lecture', 'Lettura', 'Anlatım', 'Лекция');
   		break;
	///////////////////// _s_t_mod inizio
	case 'MinimalAge':
		$type = handle_translation('Minimal Age', 'Età Minima', 'Asgari yaş', 'Минимална възраст');
		break;
	case 'MaximalAge':
		$type = handle_translation('Maximal Age', 'Età Massima', 'Maksimum yaş', 'Максимална възраст');	
		break;
	//////////////////// _s_t_mod fine
    }					
    return $type;
}

function translate_time($time) //s_t_mod Chiara: era già tutto ok
{
    switch ($time) {
	case 'minutes30':
		$time = handle_translation('30 minutes', '30 minuti', '30 dakika', '30 минути');
		break;
	case 'minutes60':
		$time = handle_translation('60 minutes', '60 minuti', '60 dakika', '60 минути');
		break;	
	case 'minutes90':
		$time = handle_translation('90 minutes', '90 minuti', '90 dakika', '90 минути');
		break;
	case 'minutes120p':
		$time = handle_translation('+120 minutes', '+120 minuti', '+120 dakika', '+120 минутри');
		break;			
	}
	return $time;
}

function translate_category($category) 
{
    switch ($category) {
	case 'Entrepreneurial Vision':
		$category = handle_translation('Entrepreneurial Vision', 'Visione Imprenditoriale', 'Girişimcilik Vizyonu', 'Предприемаческа визия');
		break;
	case 'Personal Development':
		$category = handle_translation('Personal Development', 'Sviluppo Personale', 'Kişisel Gelişim', 'Личностно развитие');
		break;	
	case 'Communication Skills':
		$category = handle_translation('Communication Skills', 'Abilità Comunicative', 'İletişim Becerileri', 'Комуникационни умения');
		break;
	case 'Economic Skills':
		$category = handle_translation('Economic Skills', 'Competenze in Economia', 'Ekenomik Beceriler', 'Икономически умения');
		break;	
	case 'Technical Skills':
		$category = handle_translation('Technical Skills', 'Abilità Informatiche', 'Teknik Beceriler', 'Технически умения');
		break;	

	//s_t_mod Hui: traduzione da italiano a inglese
	case 'Visione imprenditoriale':
		$category = handle_translation('Entrepreneurial Vision', 'Visione Imprenditoriale', 'Girişimcilik Vizyonu', 'Предприемаческа визия');
		break;
	case 'Sviluppo Personale':
		$category = handle_translation('Self-Growth', 'Sviluppo Personale', 'Kişisel Gelişim', 'Личностно развитие');
		break;	
	case 'Abilità Comunicative':
		$category = handle_translation('Communication Skills', 'Abilità Comunicative', 'İletişim Becerileri', 'Комуникационни умения');
		break;
	case 'Competenze in Economia':
		$category = handle_translation('Business Capabilities', 'Competenze in Economia', 'Ekenomik Beceriler', 'Икономически умения');
		break;	
	case 'Abilità; Informatiche':
		$category = handle_translation('Computer Science skills', 'Abilità Informatiche', 'Teknik Beceriler', 'Технически умения');
		break;		
	}
	return $category;
}
		
// TO CHANGE		
function translate_engine($engine) { //s_t_mod Chiara: fatta una modifica a "Proceed >>"
    switch ($engine) {
	case 'Add modules through metadata':
		$engine = handle_translation('Add modules through metadata', 'Aggiungi moduli da metadati', 'Add modules through metadata', 'Add modules through metadata');
		break;
	case 'NETT parameters - 1/3': 
		$engine = handle_translation('NETT parameters - 1/3', 'Parametri NETT - 1/3', 'NETT parameters - 1/3', 'NETT parameters - 1/3');
		break;
	case 'NETT parameters - 2/3': 
		$engine = handle_translation('NETT parameters - 2/3', 'Parametri NETT - 2/3', 'NETT parameters - 2/3', 'NETT parameters - 2/3');
		break;
	case 'NETT parameters - 3/3': 
		$engine = handle_translation('NETT parameters - 3/3', 'Parametri NETT - 3/3', 'NETT parameters - 3/3', 'NETT parameters - 3/3');
		break;
	case 'Language':
		$engine = handle_translation('Language', 'Lingua', 'Language', 'Language');
		break;
	case 'Category':
		$engine = handle_translation('Category', 'Categoria', 'Category', 'Category');
		break;
	case 'Required skills':	//s_t_mod Hui: da "background" a "required skills"
		$engine = handle_translation('Required skills', 'Abilità Richieste', 'Background', 'Background');
		break;
	case 'Acquired skills':
		$engine = handle_translation('Acquired skills', 'Abilità Acquisite', 'Acquired skills', 'Acquired skills');
		break;
	case 'Proceed >>':
		$engine = handle_translation('Proceed >>', 'Procedi >>', 'Proceed >>', 'Proceed >>'); //s_t_mod Aggiunto ">>" anche a Proceed
		break;
	case 'Select the modules that compose the course': 
		$engine = handle_translation('Select the modules that compose the course', 'Seleziona i moduli che compongono il corso', 'Select the modules that compose the course', 'Select the modules that compose the course');
		break;
	case 'Modules':
		$engine = handle_translation('Modules', 'Moduli', 'Modules', 'Modules');
		break;
	case 'Selected':
		$engine = handle_translation('Selected', 'Selezionati', 'Selected', 'Selected');
		break;
	case 'Submit':
		$engine = handle_translation('Submit', 'Invia', 'Submit', 'Submit');
		break;
	case 'Learning Resource Type':
		$engine = handle_translation('Learning Resource Type', 'Tipo di risorsa', 'Learning Resource Type', 'Learning Resource Type');
		break;
	case 'Difficulty':
		$engine = handle_translation('Difficulty', 'Difficoltà', 'Difficulty', 'Difficulty');
		break;
	case 'Minimal Age':
		$engine = handle_translation('Minimal Age', 'Età Minima', 'Asgari yaş', 'Минимална възраст');
		break;
	case 'Maximal Age':
		$engine = handle_translation('Maximal Age', 'Età Massima', 'Maksimum yaş', 'Максимална възраст');	
		break;

	case 'Format':
		$engine = handle_translation('Format', 'Formato', 'Format', 'Format');
		break;
	case 'Typical Learning Time':
		$engine = handle_translation('Typical Learning Time', 'Tempo d\'Apprendimento', 'Typical Learning Time', 'Typical Learning Time');
		break;
	case 'Keywords (separator ", ")':
		$engine = handle_translation('Keywords (separator ", ")', 'Keywords (separatore ", ")', 'Keywords (separator ", ")', 'Keywords (separator ", ")');
		break;
	case '<< Back':
		$engine = handle_translation('<< Back', '<< Indietro', '<< Back', '<< Back');
		break;
	case 'Please wait...':
		$engine = handle_translation('Please wait...', 'Attendere prego...', 'Please wait...', 'Please wait...');
		break;
	case 'Select options':
		$engine = handle_translation('Select options', 'Seleziona opzioni', 'Select options', 'Select options');
		break;
	
    }
    return $engine;
}		

function find_image($r_type)
{
    switch ($r_type) {
    	case 1:
        	$file_name = "assign";
        	break;
	case 3:
        	$file_name = "book";
        	break;
	case 4:
        	$file_name = "chat";
        	break;
	case 5:
        	$file_name = "choice";
        	break;
	case 6:
        	$file_name = "data";
        	break;
	case 8:
        	$file_name = "folder";
        	break;
	case 9:
        	$file_name = "forum";
        	break;
	case 10:
        	$file_name = "glossary";
        	break;
	case 11:
        	$file_name = "imscp";
        	break;
	case 12:
        	$file_name = "label";
        	break;
	case 13:
        	$file_name = "lesson";
        	break;
	case 14:
        	$file_name = "lti";
        	break;
	case 15:
        	$file_name = "page";
        	break;
	case 16:
        	$file_name = "quiz";
        	break;
	case 17:
        	$file_name = "resource";
        	break;
	case 18:
        	$file_name = "scorm";
        	break;
	case 19:
        	$file_name = "survey";
        	break;
	case 20:
        	$file_name = "url";
        	break;
	case 21:
        	$file_name = "wiki";
        	break;
	case 22:
        	$file_name = "workshop";
        	break;
    }

    return $file_name;
}


function convert_RS($term){ //s_t_mod Chiara: fatte alcune modifiche sia in inglese che in italiano
    switch ($term) {
    	//INDEX
    	case 'COURSE FEATURES':
        	$term = handle_translation("COURSE FEATURES", "CARATTERISTICHE CORSO", "COURSE FEATURES", "COURSE FEATURES");
        	break;
    	case 'Knowledge Area':
        	$term = handle_translation("Course category", "Categoria corso", "Knowledge Area", "Knowledge Area");
        	break;
    	case 'Course Name':
        	$term = handle_translation("Course Name", "Nome del Corso", "Course Name", "Course Name");
        	break;
    	case 'Continue':
        	$term = handle_translation("Continue", "Prosegui", "Continue", "Continue");
        	break;
    	case 'Insert course name':
        	$term = handle_translation("Insert course name", "Inserisci nome corso", "Insert course name", "Insert course name");
        	break;
		case 'Recommender System Parameters':
        	$term = handle_translation("Recommender System Parameters", "Parametri Sistema di Raccomandazione", "Recommender System Parameters", "Recommender System Parameters");
        	break;
    	case 'Reset Values':
        	$term = handle_translation("Reset Values", "Ripristina Valori", "Reset Values", "Reset Values");
        	break;
    	case 'Number of Sections':
        	$term = handle_translation("Number of Sections", "Numero di Sezioni", "Number of Parts", "Number of Parts");
        	break;
    	case 'Threshold':
        	$term = handle_translation("Minimum Resource Score", "Punteggio minimo delle risorse", "Threshold", "Threshold"); //s_t_mod Chiara: a "minumum resourse score" aggiunte lettere maiuscole
        	break;
    	case 'Rules Support':
        	$term = handle_translation("Rules Support", "Supporto Regole", "Rules Support", "Rules Support");
        	break;
    	case 'Min Keywords':
        	$term = handle_translation("Min Keywords", "Keywords Minime", "Min Keywords", "Min Keywords");
        	break;
    	case 'Keywords':
        	$term = handle_translation("Keywords", "Keywords", "Keywords", "Keywords");
        	break;
    	case 'Max Keywords':
        	$term = handle_translation("Max Keywords", "Keywords Massime", "Max Keywords", "Max Keywords");
        	break;
    	case 'Rules per Page':
        	$term = handle_translation("Rules per Page", "Regole per Pagina", "Rules per Page", "Rules per Page");
        	break;
		case 'Entrepreneurial Vision':
			$term = handle_translation("Entrepreneurial Vision", "Visione Imprenditoriale", "Entrepreneurial Vision", "Entrepreneurial Vision");
			break;
		case 'Personal Development':
			$term = handle_translation("Personal Development", "Sviluppo Personale", "Personal Development", "Personal Development");
			break;	
		case 'Communication Skills':
			$term = handle_translation('Communication Skills', "Abilità Comunicative", 'Communication Skills', 'Communication Skills'); //s_t_mod Chiara: cambiato " a' " con "à"
			break;
		case 'Economic Skills':
			$term = handle_translation("Economic Skills", "Competenze in Economia", "Economic Skills", "Economic Skills");
			break;	
		case 'Technical Skills':
			$term = handle_translation("Technical Skills", "Abilità Informatiche", "Technical Skills", "Technical Skills");	//s_t_mod Chiara: cambiato " a' " con "à"
			break;
		case 'Probability and Statistics':
			$term = handle_translation("Probability and Statistics", "Probabilità e Statistica", "Probability and Statistics", "Probability and Statistics");	
			break;
		case 'For the proper functioning of the service SHOULD NOT be used commands/buttons "Next" and "Back" of the browser.':
			$term = handle_translation('For the proper functioning of the service SHOULD NOT be used commands/buttons \"Next\" and \"Back\" of the browser.', 'Per il corretto funzionamento del servizio NON DEVONO essere utilizzati i comandi/pulsanti \"Avanti\" e \"Indietro\" del browser.', 'For the proper functioning of the service SHOULD NOT be used commands/buttons \"Next\" and \"Back\" of the browser.', 'For the proper functioning of the service SHOULD NOT be used commands/buttons \"Next\" and \"Back\" of the browser.');
			break;
		case 'Remember to use navigation functions that the system offers.':
			$term = handle_translation('Remember to use navigation functions that the system offers.', 'Si ricorda di utilizzare le funzioni di navigazione che offre il sistema.', 'Remember to use navigation functions that the system offers.', 'Remember to use navigation functions that the system offers.');
			break;
		case 'Show advanced RS options':
			$term = handle_translation('Show advanced options', 'Mostra opzioni avanzate', 'Show advanced RS options', 'Show advanced RS options'); ////s_t_mod Chiara: tolto "RS" dalla traduzione in inglese
			break;
		//aggiunta traduzionemancante
		case 'Number of sections the course will contain':
			$term = handle_translation("Number of sections the course will contain", "Numero di sezione in cui verrà diviso il corso", "Number of sections the course will contain", "Number of sections the course will contain");
			break;
		case 'Cancel':
			$term = handle_translation("Cancel", "Cancella", "Cancel", "Cancel");
			break;
		//STEP1	
		case 'MODULES':
			$term = handle_translation("MODULES", "MODULI", "MODULES", "MODULES");
			break;
		case 'Compiling part: ':	
			$term = handle_translation("Compiling part: ", "Parte in Compilazione: ", "Compiling part: ", "Compiling part: ");	
			break;
		case ' of ':
			$term = handle_translation(" of ", " di ", " of ", " of ");
			break;
		case 'Two or more selected rules are interpreted as the conjunction of them':
			$term = handle_translation("Two or more selected rules are interpreted as the conjunction of them", "Due o più regole selezionate vengono interpretate come congiunzione di esse", "Two or more selected rules are interpreted as the conjunction of them", "Two or more selected rules are interpreted as the conjunction of them");
			break;
		case 'Significant Terms':
			$term = handle_translation("Significant Terms", "Termini Significativi", "Significant Terms", "Significant Terms");
			break;
		case 'Completing part:':	
			$term = handle_translation("Completing part:", "Parte in Compilazione:", "Completing part:", "Completing part:");	
			break;
		case ' on ':
			$term = handle_translation(" on ", " su ", " on ", " on ");
			break;	
		case 'Going back at this time you will lose all settings.':
			$term = handle_translation("Going back at this time you will lose all settings.", "Tornando indietro in questa fase perderai tutte le impostazioni.", "Going back at this time you will lose all settings.", "Going back at this time you will lose all settings.");
			break;
		//STEP2
		case 'RULES':
			$term = handle_translation("RULES", "REGOLE", "RULES", "RULES");
			break;
		case 'Number of Modules:':	
			$term = handle_translation("Number of Modules:", "Numero di Moduli:", "Number of Modules:", "Number of Modules:");	
			break;
		case 'CONTINUE':
			$term = handle_translation("Continue", "Prosegui", "CONTINUE", "CONTINUE");
			break;
		case 'Communication Skills':
			$term = handle_translation("Communication Skills", "Abilita' Comunicative", "Communication Skills", "Communication Skills");
			break;
		case 'Due to updates will not be possible go back.':
			$term = handle_translation("Due to updates will not be possible go back.", "A causa di aggiornamenti non sarà possibile tornare indietro.", "Due to updates will not be possible go back.", "Due to updates will not be possible go back.");
			break;
		case 'ARE YOU SURE YOU PROCEED?':
			$term = handle_translation("ARE YOU SURE YOU PROCEED?", "SEI SICURO DI CONTINUARE?", "ARE YOU SURE YOU PROCEED?", "ARE YOU SURE YOU PROCEED?");
			break;
		case 'Select rules':
			//s_t_mod Chiara : nuova descrizione in Italiano
			//$term = handle_translation("Select rules", "Selezione delle regole", "Select rules", "Select rules");
			$term = handle_translation("SELECT RULES", "SCEGLI LE REGOLE", "SELECT RULES", "SELECT RULES"); //s_t_mod Chiara: tutto maiuscolo anche in inglese
			break;
		case 'step2 advice':
			//s_t_mod Chiara : nuova descrizione in Italiano e in Inglese
			//$term = handle_translation("Here are some rules for making a first selection of resources that your course will contain. if you follow multiple rules, only the resources that respect all the set rules will be proposed. The selection is not mandatory.", "Di seguito ti vengono proposte alcune regole per effettuare una prima selezione delle risorse che conterrà il tuo corso. se segli piu regole ti verrano proposte solo le risorse che rispettano tutte le regole impostate. La selezione non è obbligatoria.", "Select rules", "Select rules");
			$term = handle_translation("Here some rules are proposed to you, which will help the System to refine the selection of resources that you can later include in your course.<br>If you choose multiple rules, you can expect proposals only resources that respect all the selected rules.<br>The selection is not mandatory.", "Di seguito ti vengono proposte alcune regole, le quali aiuteranno il Sistema ad affinare la selezione delle risorse che successivamente potrai inserire nel tuo corso.<br>Se segli più regole ti verrano proposte solo le risorse che rispettano tutte le regole selezionate.<br>La selezione non è obbligatoria.", "Select rules", "Select rules"); 
			break;
		case 'No rule':
			$term = handle_translation("No rule found","Non è presente nessuna regola","No rule found","No rule found");
			break;
			case 'CONTINUA':
			$term = handle_translation("CONTINUE","CONTINUA","CONTINUE","CONTINUE");
			break;
		//STEP3	
		case 'KEYWORDS':
			//s_t_mod Chiara : nuova descrizione in Italiano
			//$term = handle_translation("SELECT KEYWORDS", "SELEZIONI DELLE KEYWORDS", "KEYWORDS", "KEYWORDS");
			$term = handle_translation("SELECT KEYWORDS", "SCEGLI LE KEYWORDS", "KEYWORDS", "KEYWORDS");
			break;
		case 'Number of resources:':	
			$term = handle_translation("Number of resources:", "Numero di Risorse:", "Number of resources:", "Number of resources:");	
			break;
		case 'More Keywords':
			$term = handle_translation("MORE KEYWORDS", "PIU KEYWORDS", "MORE KEYWORDS", "MORE KEYWORDS");
			break;
		case 'Summary previous steps':
			$term = handle_translation("Summary previous steps", "Riepilogo step precedenti", "Summary previous steps", "Summary previous steps");
			break;
		case "The operation in progress may take some time. Don't reload the page!":
			$term = handle_translation("The operation in progress may take some time. Don't reload the page!", "L'operazione in corso potrebbe richiedere qualche istante. Non ricaricare la pagina!", "The operation in progress may take some time. Don't reload the page!", "The operation in progress may take some time. Don't reload the page!");
			break;
		case 'Restore Keywords':
			$term = handle_translation("RESTORE KEYWORDS", "RIPRISTINA KEYWORDS", "Restore Keywords", "Restore Keywords");
			break;
		case 'BACK':
			$term = handle_translation("Back", "Indietro", "NOT HANDLE", "NOT HANDLE");
			break;
		case 'step3 advice':
			//s_t_mod Chiara : nuova descrizione in Italiano e in Inglese
			//$term = handle_translation("Here are some keywords to refine the selection of resources that your course will contain. You can also request the generation of additional keys or restore the ones suggested to you at the beginning.", "Di seguito verranno proposte alcune keywords per affinare la selezione delle risorse che conterrà il tuo corso. puoi anche richiedere la generazione di ulteriori keywords o ripristinare quelle che ti vengono suggerite all'inizio.", "NOT HANDLE", "NOT HANDLE");
			$term = handle_translation("Here some keywords are proposed to you, which will help the System to refine the selection of resources that you can later include in your course.<br>You can also request the generation of additional keys or restore the ones suggested to you at the beginning.", "Di seguito ti vengono proposte alcune keywords, le quali aiuteranno il Sistema ad affinare la selezione delle risorse che successivamente potrai inserire nel tuo corso.<br>Puoi anche richiedere la generazione di ulteriori keywords o ripristinare quelle che hai già selezionato.", "NOT HANDLE", "NOT HANDLE");
			break;
		case 'No keywords found':
			$term = handle_translation("No keywords found", "Keywords non trovate", "NOT HANDLE", "NOT HANDLE");
			break;
		case 'keywords exhausted':
			$term = handle_translation("Exhausted keywords", "Keywords esaurite", "NOT HANDLE", "NOT HANDLE"); //s_t_mod Chiara: messa iniziale maiuscola a "exhausted"
			break;
		case 'No keyword selected':
			$term = handle_translation("No keyword selected", "Non hai scelto nessun keyword.<br>", "NOT HANDLE", "NOT HANDLE");
			break;
		case 'No resources found':
			$term = handle_translation("No resources found", "Risorse non trovate", "NOT HANDLE", "NOT HANDLE");
			break;
		case 'Skip this phase please or back to home':
			$term = handle_translation("Skip this phase please or back to Home", "Vai al passo successivo oppure torna a Home", "Skip this phase please or back to home", "NOT HANDLE");
			break;
		case 'Go to previous step or back to home':
			$term = handle_translation("Go back to the previous step or back to Home", "Torna al passo precedente oppure torna a Home", "Go back to the previous step or back to home", "NOT HANDLE");
			break;
		//STEP4 
		case 'RESOURCES':
			//s_t_mod Chiara : nuova descrizione in Italiano e in Inglese
			//$term = handle_translation("RESOURCES", "RISORSE", "RESOURCES", "RESOURCES");
			$term = handle_translation("SELECT RESOURCES", "SCEGLI LE RISORSE", "RESOURCES", "RESOURCES");
			break;
		case 'Module Name':	
			$term = handle_translation("Module Name", "Nome Modulo", "Module Name", "Module Name");	
			break;
		case 'Insert module name':
			$term = handle_translation("Insert module name", "Inserisci nome modulo", "Insert module name", "Insert module name");
			break;
		case 'CONCLUDE':
			$term = handle_translation("CONCLUDE", "CONCLUDI", "CONCLUDE", "CONCLUDE");
			break;
		case 'Resource Preview':
			$term = handle_translation("Resource Preview", "Anteprima Risorsa", "Resource Preview", "Resource Preview");
			break;
		case 'Select your Resources':	
			$term = handle_translation("Select your Resources", "Scegli le Risorse", "Select your Resources", "Select your Resources");	
			break;
		case 'step4 advice1':
			$term = handle_translation("The following are the resources that you have selected thanks to your choices in the previous steps.<br>Choose from the box on the left the resources you want to insert in your runs: the selected resources will move to the box on the right.<br>Remember to name the section of the course that will contain the resources!", "Di seguito ti vengono proposte le risorse che hai selezionato grazie alle tue scelte negli step precedenti.<br>Scegli dal box di sinistra le risorse che vuoi inserire nel tuo corso: le risorse selezionate si sposteranno nel box a destra.<br>Ricorda di assegnare un nome alla sezione del corso che conterrà le risorse!", "not handle", "not handle");
			break;
		
		//STEP5
		case 'COURSE RECAP':
			$term = handle_translation("Course features", "Caratteristiche del corso", "COURSE RECAP", "COURSE RECAP");
			break;	
		case 'COURSE NAME':
			$term = handle_translation("COURSE NAME", "NOME DEL CORSO", "COURSE NAME", "COURSE NAME");
			break;
		case 'KNOWLEDGE AREA':	
			$term = handle_translation("KNOWLEDGE AREA", "AREA DI CONOSCENZA", "KNOWLEDGE AREA", "KNOWLEDGE AREA");	
			break;
		case 'PARTS':
			$term = handle_translation("Number of Sections", "Numero di sezioni", "PARTS", "PARTS");
			break;
		case 'Resource Name: ':
			$term = handle_translation("Resource Name: ", "Nome Risorsa: ", "Resource Name: ", "Resource Name: ");
			break;
		case 'Resource Type: ':	
			$term = handle_translation("Resource Type: ", "Tipo di Risorsa: ", "Resource Type: ", "Resource Type: ");	
			break;	
		case 'Resource Id: ':	
			$term = handle_translation("Resource Id: ", "Id Risorsa: ", "Resource Id: ", "Resource Id: ");	
			break;	
		case 'CONTINUE TO COMPILING':
			$term = handle_translation("CONTINUE TO COMPILING", "PROSEGUI CON LA COMPILAZIONE", "CONTINUE TO COMPILING", "CONTINUE TO COMPILING");
			break;	
		case 'CONTINUE LATER':
			$term = handle_translation("CONTINUE LATER", "PROSEGUI PIU' TARDI", "CONTINUE LATER", "CONTINUE LATER");
			break;
		case 'Create Course':	
			$term = handle_translation("Create Course", "Crea il Corso", "Create Course", "Create Course");	
			break;
		case 'Courses\' Macrocategories':	
			$term = handle_translation("Select the most suitable category for your course from the available categories", "Segli fra le categorie disponibili quella più adatta al tuo corso", "Courses' Macrocategories", "Courses' Macrocategories");	
			break;
		case 'Threshold info':	
			$term = handle_translation("Choose the minimum score attributed to the resources. The scores are assigned by grades on a scale of 1-5, by the teachers present on whoteach", "Scegli il punteggio minimo attribuito alle risorse. I punteggi sono assegnati tramite i voti su  scala da 1-5, dai docenti presenti su whoteach", "Minimum threshold that defines course quality. Beyond this threshold, the course is considered good.", "Minimum threshold that defines course quality. Beyond this threshold, the course is considered good.");	
			break;
		case 'Minimum number of keywords that will be displayed in the "Keywords" step.':
			$term = handle_translation("Minimum number of keywords that will be displayed in the \"Keywords\" step.", "Numero minimo di keywords che verranno visualizzate nello step \"Keywords\"", "Minimum number of keywords that will be displayed in the \"Keywords\" step.", "Minimum number of keywords that will be displayed in the \"Keywords\" step.");
			break;	
		case 'Maximum number of keywords that will be displayed in the "Keywords" step.':
			$term = handle_translation("Maximum number of keywords that will be displayed in the \"Keywords\" step.", "Numero massimo di keywords che verranno visualizzate nello step \"Keywords\"", "Maximum number of keywords that will be displayed in the \"Keywords\" step.", "Maximum number of keywords that will be displayed in the \"Keywords\" step.");
			break;
		case 'Maximum number of rules that will be displayed in the "Rules" step.':	
			$term = handle_translation("Maximum number of rules that will be displayed in the \"Rules\" step.", "Numero massimo di regole che verranno visualizzate nello step \"Regole\"", "Maximum number of rules that will be displayed in the \"Rules\" step.", "Maximum number of rules that will be displayed in the \"Rules\" step.");	
			break;
		case 'PLEASE INSERT MODULE NAME':	
			$term = handle_translation("PLEASE INSERT MODULE NAME", "INSERISCI NOME MODULO", "PLEASE INSERT MODULE NAME", "PLEASE INSERT MODULE NAME");	
			break;
		case 'PLEASE SELECT SOME RESOURCES':	
			$term = handle_translation("PLEASE SELECT SOME RESOURCES", "SELEZIONA UNA O PIU' RISORSE", "PLEASE SELECT SOME RESOURCES", "PLEASE SELECT SOME RESOURCES");	
			break;
		case 'PLEASE INSERT COURSE NAME':	
			$term = handle_translation("PLEASE INSERT COURSE NAME", "INSERISCI NOME CORSO", "PLEASE INSERT COURSE NAME", "PLEASE INSERT COURSE NAME");	
			break;
		case 'PLEASE SELECT SOME RULES':	
			$term = handle_translation("PLEASE SELECT SOME RULES", "SELEZIONA UNA O PIU' REGOLE", "PLEASE SELECT SOME RULES", "PLEASE SELECT SOME RULES");	
			break;
		case 'Number of parts the course will contain':	
			$term = handle_translation("Number of parts the course will contain", "Numero di parti che il corso andrà a contenere", "Number of parts the course will contain", "Number of parts the course will contain");	
			break;
		case 'This threshold defines incidence of rules':	
			$term = handle_translation("This threshold defines incidence of rules", "Questa soglia definisce il peso delle regole", "This threshold defines incidence of rules", "This threshold defines incidence of rules");	
			break;
		case 'Part not inserted yet':	
			$term = handle_translation("Part not inserted yet", "Parte non ancora inserita", "Part not inserted yet", "Part not inserted yet");	
			break;
		case 'Keywords previously selected':	
			$term = handle_translation("Keywords previously selected", "Keywords precedentemente selezionate", "Keywords previously selected", "Keywords previously selected");	
			break;
		case 'Required Field!':	
			$term = handle_translation("Required Field!", "Campo Obbligatorio!", "Required Field!", "Required Field!");	
			break;
		case 'Add a course through Recommender System':	
			$term = handle_translation("Add a course through Recommender System", "Aggiungi un corso tramite il Sistema di Raccomandazione", "Add a course through Recommender System", "Add a course through Recommender System");	
			break;	
		case 'FOR':
				$term = handle_translation("FOR", "PER", "FOR", "FOR");
				break;
		case 'Riepilogo delle scelte':
				$term = handle_translation("SUMMARY OF CHOICES", "RIEPILOGO DELLE SCELTE", "Summary of choices", "Summary of choices"); //s_t_mod Chiara: messo tutto in maiuscolo anche per traduzione in Inglese
				break;
		case 'step5 advice':
				$term = handle_translation("Below is a summary of the choices you have made in the various configuration steps of the course. If you want to change some choices you can go back to the specific step using the back button.", "Di seguito ti viene mostrato un riepilogo delle scelte che hai effettuato nei diversi step di configurazione del corso. Se vuoi modificare alcune scelte puoi tornare allo step specifico usando il bottone indietro.", "FOR", "FOR");
				break;
		case 'Selezione delle risorse':
				$term = handle_translation("Resource selection", "Selezione delle risorse", "Selezione delle risorse", "Selezione delle risorse");
				break;
		//create_course.php
		case 'Course is building....':
				$term = handle_translation("Course is building....", "Creazione corso.....", "Course is building....", "Course is building....");
				break;
				
		//progressbar 
		case 'Choose rules':
				$term = handle_translation("Choose rules", "Scelta delle regole", "not handle", "not handle");
				break;
		case 'Filter Keyword':
				$term = handle_translation("Filter Keywords", "Filtraggio delle keywords", "not handle", "not handle");
				break;
		case 'Modulo & Keyword':
				$term = handle_translation("Modulo & Keyword", "Modulo & Keyword", "not handle", "not handle");
				break;
		case 'Continue or create':
				$term = handle_translation("Continue or create", "Continua o Crea", "not handle", "not handle");
				break;
		
	}
    return $term;
}

//_s_t_mod_ Aggiunta funzione traduzione da lms/review + inserito nuovi case per le parole in Italiano
function translate_element($review) { //s_t_mod Chiara: dovrebbe essere tutto giusto
    switch ($review) {
	case 'Submitted':
		$review = handle_translation('Submitted', 'Sottoposti', 'Gönderildi', 'Предадено');
		break;
	case 'Assigned':
		$review = handle_translation('Assigned', 'Assegnati', 'Atanmış', 'Възложен');
		break;
	case 'Published':
		$review = handle_translation('Published', 'Pubblicati', 'Yayımlandı', 'Публикуван');
		break;	
	case 'View modules:':
		$review = handle_translation('View modules:', 'Vedi moduli:', 'Modülleri gör:', 'Покажи модули:');
		break;
	case 'Module':
		$review = handle_translation('Module', 'Modulo', 'Modül', 'модул');	
		break;	
	case 'Course':
		$review = handle_translation('Course', 'Corso', 'Ders', 'курс');	
		break;	
	case 'Assign the module to an expert':
		$review = handle_translation('Assign the module to an expert', 'Assegna il modulo a un expert', 'Modülü bir uzmana ata', 'Модулът е възложен на експерт');	
		break;	
	case 'Publish the module':
		$review = handle_translation('Publish the module', 'Pubblica il modulo', 'Modülü yayımla', 'Публикуване на модул');	
		break;
	case 'Assign':
		$review = handle_translation('Assign', 'Assegna', 'Ata', 'Възложи');
		break;	
	case 'Publish':
		$review = handle_translation('Publish', 'Pubblica', 'Yayımla', 'Публикувай');
		break;	
	case 'View expert reviews':
		$review = handle_translation('View expert reviews', 'Vedi revisione expert', 'Uzman yorumları gör', 'Покажи рецензиите на експертите');
		break;	
	case 'Completed':
		$review = handle_translation('Completed', 'Completato', 'Tamamlandı', 'Приключен');
		break;	
	case 'Status':
		$review = handle_translation('Status', 'Stato', 'Durum', 'Статус');
		break;	
	case 'View your review':
		$review = handle_translation('View your review', 'Vedi la tua revisione', 'Kendi yorumunu gör', 'Покажи своята рецензия');
		break;	
	case 'View':
		$review = handle_translation('View', 'Vedi', 'Görüntüle', 'Покажи');
		break;
	case 'Approved':
		$review = handle_translation('Approved', 'Approvato', 'Onaylandı', 'Одобрен');
		break;	
	case 'No modules':
		$review = handle_translation('No modules', 'Nessun modulo', 'No modules', 'No modules');
		break;
	case 'Go back':
		$review = handle_translation('Go back', 'Indietro', 'Geri dön', 'Върни се назад');
		break;	
	case 'Experts Available':
		$review = handle_translation('Experts Available', 'Expert disponibili', 'Uzmanlar uygun', 'Експерти на разположение');
		break;	
	case 'Experts assigned':
		$review = handle_translation('Experts assigned', 'Expert assegnati', 'Uzman atanmış', 'Назначени експерти');
		break;	
	case 'Remove':
		$review = handle_translation('Remove', 'Rimuovi', 'Kaldır', 'Изтрий');
		break;	
	case 'No experts':
		$review = handle_translation('No experts', 'Nessun expert', 'No experts', 'No experts');
		break;	
	case 'Resources: Metadata':
		$review = handle_translation('Resources: Metadata', 'Risorse: Metadati', 'Kaynaklar: metadata', 'Ресурси: метаданни');
		break;	
	case 'No experts to be assigned':
		$review = handle_translation('No experts to be assigned', 'Nessun expert da assegnare', 'Hiçbir uzman atanmadı', 'Няма експерт, който да бъде номиниран');
		break;	
	case 'Review of ':
		$review = handle_translation('Review of ', 'Revisione di ', 'Nin yorumu ', 'Рецензия на ');
		break;
	case 'Invitation Accepted':
		$review = handle_translation('Invitation Accepted', 'Invito Accettato', 'Davet kabul edildi', 'Поканата е приета');
		break;	
	case 'Review Completed':
		$review = handle_translation('Review Completed', 'Revisione Completata', 'Yorum tamamlandı', 'Рецензията е приключена');
		break;	
	case 'Decision':
		$review = handle_translation('Decision', 'Decisione', 'Karar', 'Решение');
		break;
	case 'No decision':
		$review = handle_translation('No decision', 'Nessuna decisione', 'Karar yok', 'Няма решение');
		break;
	case 'Your Review':
		$review = handle_translation('Your Review', 'La tua Revisione', 'Kendi yorumunuz', 'Твоята рецензия');
		break;
	case ' Metadata':
		$review = handle_translation(' Metadata', ' Metadati', ' Metadata', ' Метаданни');
		break;	
	case 'Accept':
		$review = handle_translation('Accept', 'Accetta', 'Kabul et', 'Приеми');
		break;	
	case 'Reject':
		$review = handle_translation('Reject', 'Rifiuta', 'Reddet', 'Откажи');
		break;		
	case 'Accepted':
		$review = handle_translation('Accepted', 'Accettati', 'Kabul edilen', 'Признат');
		break;	
	case 'Review':
		$review = handle_translation('Review', 'Revisione', 'Yorum', 'Рецензия');
		break;	
	case 'Reviewed':
		$review = handle_translation('Reviewed', 'Revisionati', 'Yorum', 'Рецензия');
		break;	
	case 'Minor revision':
		$review = handle_translation('Minor revision', 'Revisione secondaria', 'Minor revision', 'Minor revision');
		break;
	case 'Major revision':
		$review = handle_translation('Major revision', 'Revisione principale', 'Major revision', 'Major revision');
		break;	
	case 'Rejected':
		$review = handle_translation('Rejected', 'Rifiutato', 'Rejected', 'Rejected');
		break;		
			
	// Category		
	case 'Entrepreneurial Vision':
		$review = handle_translation('Entrepreneurial Vision', 'Visione Imprenditoriale', 'Girişimcilik Vizyonu', 'Предприемаческа визия');
		break;
	case 'Personal Development':
		$review = handle_translation('Personal Development', 'Sviluppo Personale', 'Kişisel Gelişim', 'Личностно развитие');
		break;	
	case 'Communication Skills':
		$review = handle_translation('Communication Skills', 'Abilità Comunicative', 'İletişim Becerileri', 'Комуникационни умения');
		break;
	case 'Economic Skills':
		$review = handle_translation('Economic Skills', 'Competenze in Economia', 'Ekenomik Beceriler', 'Икономически умения');
		break;	
	case 'Technical Skills':
		$review = handle_translation('Technical Skills', 'Abilità Informatiche', 'Teknik Beceriler', 'Технически умения');
		break;	
		
	// Difficulty	
	case 'Very easy':
    		$review = handle_translation('Very easy', 'Molto facile', 'Çok Kolay', 'Много лесно');
    		break;
    	case 'Easy':
    		$review = handle_translation('Easy', 'Facile', 'Kolay', 'Лесно');
    		break;
    	case 'Medium':
    		$review = handle_translation('Medium', 'Media', 'Orta', 'Средно');
    		break;
    	case 'Difficult':
    		$review = handle_translation('Difficult', 'Difficile', 'Zor', 'Трудно');
    		break;
    	case 'Very difficult':
    		$review = handle_translation('Very difficult', 'Molto difficile', 'Çok Zor', 'Çok Zor');
    		break;	
    			
    	// Category 1: Entrepreneurial Vision
	case 'Proactivity':
		$review = handle_translation('Proactivity', 'Proattività', 'Proaktiflik', 'проактивност');
		break;
	case 'Entrepreneurial behaviors and attitudes':
		$review = handle_translation('Entrepreneurial behaviors and attitudes', 'Attività e attitudini imprenditoriali', 'Girişimsel davranış ve tutumlar', 'Предприемаческите нагласи и поведения');
		break;
	case 'Leadership':
		$review = handle_translation('Leadership', 'Capacità di comando', 'Liderlik', 'Лидерски умения');
		break;
	case 'Self-evaluation':
		$review = handle_translation('Self-evaluation', 'Autovalutazione', 'Öz-değerlendirme', 'Самооценката');
		break;
	case 'Self-organization':
		$review = handle_translation('Self-organization', 'Auto-organizzazione', 'Öz-örgütlenme', 'Самоорганизация');
		break;
	case 'Innovative thinking':
		$review = handle_translation('Innovative thinking', 'Pensiero innovativo', 'Yenilikçi düşünme', 'Иновативно мислене');
		break;
	case 'Creative thinking':
		$review = handle_translation('Creative thinking', 'Pensiero creativo', 'Yaratıcı düşünme', 'Творческо мислене');
		break;
	case 'Opportunities Management':
		$review = handle_translation('Opportunities Management', 'Gestione delle opportunità', 'Fırsat Yönetimi', 'Възможности за управление');
		break;
	case 'Ability to promote initiatives':
		$review = handle_translation('Ability to promote initiatives', 'Capacità di promuovere iniziative', 'Girişimi teşvik edebilme', 'Насърчаване на инициативността');
		break;
	case 'Management Skills':
		$review = handle_translation('Management Skills', 'Capacità Amministrative', 'Yönetim Becerileri', 'Управленски умения');
		break;
	case 'Risk Management':
		$review = handle_translation('Risk Management', 'Gestione del Rischio', 'Risk Yönetimi', 'Управление на риска');
		break;	
		
	case 'Proattività':
		$review = handle_translation('Proactivity', 'Proattività', 'Proaktiflik', 'проактивност');
		break;
	case 'Attività e attitudini imprenditoriali':
		$review = handle_translation('Entrepreneurial behaviors and attitudes', 'Attività e attitudini imprenditoriali', 'Girişimsel davranış ve tutumlar', 'Предприемаческите нагласи и поведения');
		break;
	case 'Capacità di comando':
		$review = handle_translation('Leadership', 'Capacità di comando', 'Liderlik', 'Лидерски умения');
		break;
	case 'Autovalutazione':
		$review = handle_translation('Self-evaluation', 'Autovalutazione', 'Öz-değerlendirme', 'Самооценката');
		break;
	case 'Auto-organizzazione':
		$review = handle_translation('Self-organization', 'Auto-organizzazione', 'Öz-örgütlenme', 'Самоорганизация');
		break;
	case 'Pensiero innovativo':
		$review = handle_translation('Innovative thinking', 'Pensiero innovativo', 'Yenilikçi düşünme', 'Иновативно мислене');
		break;
	case 'Pensiero creativo':
		$review = handle_translation('Creative thinking', 'Pensiero creativo', 'Yaratıcı düşünme', 'Творческо мислене');
		break;
	case 'OGestione delle opportunità':
		$review = handle_translation('Opportunities Management', 'Gestione delle opportunità', 'Fırsat Yönetimi', 'Възможности за управление');
		break;
	case 'Capacità di promuovere iniziative':
		$review = handle_translation('Ability to promote initiatives', 'Capacità di promuovere iniziative', 'Girişimi teşvik edebilme', 'Насърчаване на инициативността');
		break;
	case 'Capacità Amministrative':
		$review = handle_translation('Management Skills', 'Capacità Amministrative', 'Yönetim Becerileri', 'Управленски умения');
		break;
	case 'Gestione del Rischio':
		$review = handle_translation('Risk Management', 'Gestione del Rischio', 'Risk Yönetimi', 'Управление на риска');
		break;
		
	// Category 2: Personal Development
	case 'Interpersonal Relations':
		$review = handle_translation('Interpersonal Relations', 'Relazioni Interpersonali', 'Kişilerarası İlişkiler', 'Междуличностни отношения');
		break;
	case 'Conflict Management':
		$review = handle_translation('Conflict Management', 'Gestione dei Conflitti', 'Çatışma Yönetimi', 'Управление на конфликти');	
		break;
	case 'Team working':
		$review = handle_translation('Team working', 'Lavoro di squadra', 'Takım çalışması', 'Работата в екип');	
		break;
	case 'Career Planning':
		$review = handle_translation('Career Planning', 'Pianificazione della Carriera', 'Kariyer planlama', 'Кариерно планиране');	
		break;
	case 'Job Search Skills':	
		$review = handle_translation('Job Search Skills', 'Capacità di Cercare Lavoro', 'İş Arama Becerileri', 'Умения за търсене на работа');
		break;
	case 'People Management':
		$review = handle_translation('People Management', 'Gestione del Personale', 'İnsan Yönetimi', 'Управление на хора');
		break;
	case 'Training and Professional Development':
		$review = handle_translation('Training and Professional Development', 'Formazione e Sviluppo Personali', 'Eğitim ve Mesleki Gelişim', 'Обучение и професионално развитие');
		break;
	case 'Motivation':
		$review = handle_translation('Motivation', 'Motivazione', 'Motivasyon', 'Мотивиране');
		break;
	case 'People and Performance Evaluation Skills':
		$review = handle_translation('People and Performance Evaluation Skills', 'Capacità di valutare Persone e Prestazioni', 'Kişi ve Performans Değerlendirme Becerileri', 'Умения за оценяване на хора и представяне');
		break;
	case 'Responsibility':
		$review = handle_translation('Responsibility', 'Responsabilità', 'Sorumluluk', 'Отговорност');
		break;
		
	case 'Relazioni Interpersonali':
		$review = handle_translation('Interpersonal Relations', 'Relazioni Interpersonali', 'Kişilerarası İlişkiler', 'Междуличностни отношения');
		break;
	case 'Gestione dei Conflitti':
		$review = handle_translation('Conflict Management', 'Gestione dei Conflitti', 'Çatışma Yönetimi', 'Управление на конфликти');	
		break;
	case 'Lavoro di squadra':
		$review = handle_translation('Team working', 'Lavoro di squadra', 'Takım çalışması', 'Работата в екип');	
		break;
	case 'Pianificazione della Carriera':
		$review = handle_translation('Career Planning', 'Pianificazione della Carriera', 'Kariyer planlama', 'Кариерно планиране');	
		break;
	case 'Capacità di Cercare Lavoro':	
		$review = handle_translation('Job Search Skills', 'Capacità di Cercare Lavoro', 'İş Arama Becerileri', 'Умения за търсене на работа');
		break;
	case 'Gestione del Personale':
		$review = handle_translation('People Management', 'Gestione del Personale', 'İnsan Yönetimi', 'Управление на хора');
		break;
	case 'Formazione e Sviluppo Personali':
		$review = handle_translation('Training and Professional Development', 'Formazione e Sviluppo Personali', 'Eğitim ve Mesleki Gelişim', 'Обучение и професионално развитие');
		break;
	case 'Motivazione':
		$review = handle_translation('Motivation', 'Motivazione', 'Motivasyon', 'Мотивиране');
		break;
	case 'Capacità di valutare Persone e Prestazioni':
		$review = handle_translation('People and Performance Evaluation Skills', 'Capacità di valutare Persone e Prestazioni', 'Kişi ve Performans Değerlendirme Becerileri', 'Умения за оценяване на хора и представяне');
		break;
	case 'Responsabilità':
		$review = handle_translation('Responsibility', 'Responsabilità', 'Sorumluluk', 'Отговорност');
		break;
	
	// Category 3: Communication Skills
	case 'Communications Basics':
		$review = handle_translation('Communications Basics', 'Fondamenti di Comunicazione', 'İletişimin Temelleri', 'Основни умения за общуване');
		break;
	case 'Communication Ethics':
		$review = handle_translation('Communication Ethics', 'Etica della Comunicazione', 'İletişim Etiği', 'Етика на общуването');
		break;
	case 'Information Management':
		$review = handle_translation('Information Management', 'Gestione dell\'Informazione', 'Bilgi Yönetimi', 'Информационен мениджмънт');	
		break;
	case 'Data Management':
		$review = handle_translation('Data Management', 'Gestione dei Dati', 'Veri Yönetimi', 'Управление на данни');
		break;
	case 'Information Technology Basics':
		$review = handle_translation('Information Technology Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilgi Teknolojilerinin Temelleri', 'Основни ИТ умения');	
		break;
	case 'Product and Service Marketing':
		$review = handle_translation('Product and Service Marketing', 'Marketing dei Prodotti e dei Servizi', 'Ürün ve Hizmet Pazarlama', 'Маркетинг на продукти и услуги');
		break;
	case 'Marketing Information Management':
		$review = handle_translation('Marketing Information Management', 'Gestione delle Informazioni di Marketing', 'Pazarlama Bilgi Yönetimi', 'Маркетинг на информационен мениджмънт');
		break;
	case 'Strategic Marketing  Planning':
		$review = handle_translation('Strategic Marketing Planning', 'Pianificazione Strategica di Mercato', 'Stratejik Pazarlama Planlaması', 'Стратегическо маркетинг планиране');
		break;
	
	case 'Fondamenti di Comunicazione':
		$review = handle_translation('Communications Basics', 'Fondamenti di Comunicazione', 'İletişimin Temelleri', 'Основни умения за общуване');
		break;
	case 'Etica della Comunicazione':
		$review = handle_translation('Communication Ethics', 'Etica della Comunicazione', 'İletişim Etiği', 'Етика на общуването');
		break;
	case 'Gestione dell\'Informazione':
		$review = handle_translation('Information Management', 'Gestione dell\'Informazione', 'Bilgi Yönetimi', 'Информационен мениджмънт');	
		break;
	case 'Gestione dei Dati':
		$review = handle_translation('Data Management', 'Gestione dei Dati', 'Veri Yönetimi', 'Управление на данни');
		break;
	case 'Fondamenti di Tecnologie dell\'Informazione':
		$review = handle_translation('Information Technology Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilgi Teknolojilerinin Temelleri', 'Основни ИТ умения');	
		break;
	case 'Marketing dei Prodotti e dei Servizi':
		$review = handle_translation('Product and Service Marketing', 'Marketing dei Prodotti e dei Servizi', 'Ürün ve Hizmet Pazarlama', 'Маркетинг на продукти и услуги');
		break;
	case 'Gestione delle Informazioni di Marketing':
		$review = handle_translation('Marketing Information Management', 'Gestione delle Informazioni di Marketing', 'Pazarlama Bilgi Yönetimi', 'Маркетинг на информационен мениджмънт');
		break;
	case 'Pianificazione Strategica di Mercato':
		$review = handle_translation('Strategic Marketing Planning', 'Pianificazione Strategica di Mercato', 'Stratejik Pazarlama Planlaması', 'Стратегическо маркетинг планиране');
		break;
		
	// Categoria 4: Economic Skills
	case 'Business Basics':
		$review = handle_translation('Business Basics', 'Nozioni Base di Business', 'Ticaretin Temelleri', 'Базисни бизнес умения');
		break;
	case 'Business Attitudes':	
		$review = handle_translation('Business Attitudes', 'Mentalità da Business', 'Ticaret Tutumları', 'Бизнес нагласи');
		break;
	case 'Decision Making':
		$review = handle_translation('Decision Making', 'Processo Decisionale', 'Karar Verme', 'Вземане на решение');	
		break;
	case 'Economic Culture':
		$review = handle_translation('Economic Culture', 'Conoscenze di Economia', 'Ekonomik Kültür', 'Икономическа култура');	
		break;
	case 'Financial Basics':
		$review = handle_translation('Financial Basics', 'Fondamenti di Finanza', 'Finansın Temelleri', 'Основи на финансите');
		break;
	case 'Treasury Management':
		$review = handle_translation('Treasury Management', 'Gestione della Tesoreria', 'Hazine Yönetimi', 'Управление на финанси');	
		break;
	case 'Accounting':
		$review = handle_translation('Accounting', 'Contabilità', 'Muhasebe', 'Счетоводство');
		break;
	case 'Enterprise Modeling':
		$review = handle_translation('Enterprise Modeling', 'Modellazione dei Processi Aziendali', 'Kurumsal Modelleme', 'Моделиране на производство');
		break;
	case 'Distribution Channels Management':
		$review = handle_translation('Distribution Channels Management', 'Gestione dei Canali di Distribuzione', 'Dağıtım Kanalları Yönetimi', 'Управление на каналите за дистрибуция');
		break;
	case 'Purchasing Management':
		$review = handle_translation('Purchasing Management', 'Gestione degli Acquisti', 'Satınalma Yönetimi', 'Управление на покупателните способности');
		break;
	case 'Operations Management':
		$review = handle_translation('Operations Management', 'Gestione delle Operazioni', 'Operasyon Yönetimi', 'Управление на операции и процеси');
		break;	
		
	case 'Nozioni Base di Business':
		$review = handle_translation('Business Basics', 'Nozioni Base di Business', 'Ticaretin Temelleri', 'Базисни бизнес умения');
		break;
	case 'Mentalità da Business':	
		$review = handle_translation('Business Attitudes', 'Mentalità da Business', 'Ticaret Tutumları', 'Бизнес нагласи');
		break;
	case 'Processo Decisionale':
		$review = handle_translation('Decision Making', 'Processo Decisionale', 'Karar Verme', 'Вземане на решение');	
		break;
	case 'Conoscenze di Economia':
		$review = handle_translation('Economic Culture', 'Conoscenze di Economia', 'Ekonomik Kültür', 'Икономическа култура');	
		break;
	case 'Fondamenti di Finanza':
		$review = handle_translation('Financial Basics', 'Fondamenti di Finanza', 'Finansın Temelleri', 'Основи на финансите');
		break;
	case 'Gestione della Tesoreria':
		$review = handle_translation('Treasury Management', 'Gestione della Tesoreria', 'Hazine Yönetimi', 'Управление на финанси');	
		break;
	case 'Contabilità':
		$review = handle_translation('Accounting', 'Contabilità', 'Muhasebe', 'Счетоводство');
		break;
	case 'Modellazione dei Processi Aziendali':
		$review = handle_translation('Enterprise Modeling', 'Modellazione dei Processi Aziendali', 'Kurumsal Modelleme', 'Моделиране на производство');
		break;
	case 'Gestione dei Canali di Distribuzione':
		$review = handle_translation('Distribution Channels Management', 'Gestione dei Canali di Distribuzione', 'Dağıtım Kanalları Yönetimi', 'Управление на каналите за дистрибуция');
		break;
	case 'Gestione degli Acquisti':
		$review = handle_translation('Purchasing Management', 'Gestione degli Acquisti', 'Satınalma Yönetimi', 'Управление на покупателните способности');
		break;
	case 'Gestione delle Operazioni':
		$review = handle_translation('Operations Management', 'Gestione delle Operazioni', 'Operasyon Yönetimi', 'Управление на операции и процеси');
		break;
		
	// Category 5: Technical Skills
	case 'Computer Skills':
		$review = handle_translation('Computer Skills', 'Abilità Informatiche', 'Bilgisayar Becerileri', 'Компютърни умения');
		break;
	case 'Abilità Informatiche':
		$review = handle_translation('Computer Skills', 'Abilità Informatiche', 'Bilgisayar Becerileri', 'Компютърни умения');
		break;		
	case 'IT Basics':
		$review = handle_translation('IT Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilişimin Temelleri', 'Основни ИТ умения');
		break;
	case 'Fondamenti di Tecnologie dell\'Informazione':
		$review = handle_translation('IT Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilişimin Temelleri', 'Основни ИТ умения');
		break;
	case 'IT Applications Basics':
		$review = handle_translation('IT Applications Basics', 'Applicazioni Informatiche di Base', 'Bilişim Uygulamalarının Temelleri', 'Основни умения за ИТ приложения');
		break;
	case 'Applicazioni Informatiche di Base':
		$review = handle_translation('IT Applications Basics', 'Applicazioni Informatiche di Base', 'Bilişim Uygulamalarının Temelleri', 'Основни умения за ИТ приложения');
		break;
	case 'Electronic System Tools Basics':
		$review = handle_translation('Electronic System Tools Basics', 'Fondamenti di Strumenti Elettronici', 'Elektronik Sistem Araçlarının Temelleri', 'Основни умения за работа с инструментите на електонни системи');
		break;
	case 'Fondamenti di Strumenti Elettronici':
		$review = handle_translation('Electronic System Tools Basics', 'Fondamenti di Strumenti Elettronici', 'Elektronik Sistem Araçlarının Temelleri', 'Основни умения за работа с инструментите на електонни системи');
		break;
	case 'Graphical editor':
		$review = handle_translation('Graphical editor', 'SW di Grafica', 'Çizim SW', 'Графичен редактор');
		break;
	case 'SW di Grafica':
		$review = handle_translation('Graphical editor', 'SW di Grafica', 'Çizim SW', 'Графичен редактор');
		break;
	case 'Calculation SW':
		$review = handle_translation('Calculation SW', 'SW di Calcolo', 'Hesaplama SW', 'Софтуер за изчисляван');
		break;
	case 'SW di Calcolo':
		$review = handle_translation('Calculation SW', 'SW di Calcolo', 'Hesaplama SW', 'Софтуер за изчисляван');
		break;
	case 'Project Management SW':
		$review = handle_translation('Project Management SW', 'SW di Gestione di Progetto', 'Proje Yönetimi SW', 'Софтуер за управление на проекти');
		break;
	case 'SW di Gestione di Progetto':
		$review = handle_translation('Project Management SW', 'SW di Gestione di Progetto', 'Proje Yönetimi SW', 'Софтуер за управление на проекти');
		break;
	case 'Document Management SW':
		$review = handle_translation('Document Management SW', 'SW di Gestione di Documenti', 'Belge Yönetimi SW', 'Софтуер за управление на документни потоци');
		break;
	case 'SW di Gestione di Documenti':
		$review = handle_translation('Document Management SW', 'SW di Gestione di Documenti', 'Belge Yönetimi SW', 'Софтуер за управление на документни потоци');
		break;
	case 'Planning and Control SW':
		$review = handle_translation('Planning and Control SW', 'SW di Pianificazione e Controllo', 'Planlama ve Kontrol SW', 'Софтуер за планиране и контрол');
		break;
	case 'SW di Pianificazione e Controllo':
		$review = handle_translation('Planning and Control SW', 'SW di Pianificazione e Controllo', 'Planlama ve Kontrol SW', 'Софтуер за планиране и контрол');
		break;
	case 'Simulation SW':
		$review = handle_translation('Simulation SW', 'SW di Simulazione', 'Simulasyon SW', 'Симулационен софтуер');
		break;
	case 'SW di Simulazione':
		$review = handle_translation('Simulation SW', 'SW di Simulazione', 'Simulasyon SW', 'Симулационен софтуер');
		break;
	case 'Accounting SW':
		$review = handle_translation('Accounting SW', 'SW di Contabilità', 'Muhasebe SW', 'Счетоводен софтуер');
		break;
	case 'SW di Contabilità':
		$review = handle_translation('Accounting SW', 'SW di Contabilità', 'Muhasebe SW', 'Счетоводен софтуер');
		break;
	case 'Communication SW':
		$review = handle_translation('Communication SW', 'SW di Comunicazione', 'İletişim SW', 'Комуникационен софтуер'); 
		break; 	
	case 'SW di Comunicazione':
		$review = handle_translation('Communication SW', 'SW di Comunicazione', 'İletişim SW', 'Комуникационен софтуер'); 
		break;
		
	// Language	
	case 'English':
		$review = handle_translation('English', 'Inglese', 'İngilizce', 'Английски');
		break;
	case 'Italian':
		$review = handle_translation('Italian', 'Italiano', 'İtalyanca', 'Италиански');
		break;
	case 'Bulgarian':
		$review = handle_translation('Bulgarian', 'Bulgaro', 'Bulgarca', 'Български');
		break;
	case 'Turkish':
		$review = handle_translation('Turkish', 'Turco', 'Türkçe', 'Турски');
		break;
			
	// Format		
	case 'Video':
		$review = handle_translation('Video', 'Video', 'Video', 'Видео');    
		break;
	case 'Images':
		$review = handle_translation('Images', 'Immagini', 'Görüntüler', 'снимки');    
		break;
	case 'Text':
		$review = handle_translation('Text', 'Testo', 'Metin', 'Текст');    
		break;
	case 'Audio':
		$review = handle_translation('Audio', 'Audio', 'Ses Kaydı', 'Аудио');    
		break;	
	case 'Slide':
		$format = handle_translation('Slide', 'Diapositive', 'Slayt', 'пързалка');    
		break;	
		
	// Tipi risorse	
	case 'Exercise':
   		$review = handle_translation('Exercise', 'Esercizio', 'Alıştırma', 'Упражнение');
   		break;
   	case 'Simulation':
   		$review = handle_translation('Simulation', 'Simulazione', 'Simulasyon', 'Симулация');
   		break;
   	case 'Questionnaire':
   		$review = handle_translation('Questionnaire', 'Questionario', 'Въпросник', 'Questionnaire');
   		break;
   	case 'Diagram':
   		$review = handle_translation('Diagram', 'Diagramma', 'Diyagram', 'Диаграма');
   		break;
   	case 'Figure':
   		$review = handle_translation('Figure', 'Figura', 'Şekil', 'Фигура');
   		break;
   	case 'Graph':
   		$review = handle_translation('Graph', 'Grafico', 'Grafik', 'Графика');
   		break;
   	case 'Index':
   		$review = handle_translation('Index', 'Indice', 'İndeks', 'индекс');
   		break;
   	case 'Slides':
   		$review = handle_translation('Slides', 'Diapositive', 'Slayt', 'Слайд');
   		break;
   	case 'Table':
   		$review = handle_translation('Table', 'Tabella', 'Tablo', 'Таблица');
   		break;
   	case 'Narrative text':
   		$review = handle_translation('Narrative text', 'Testo Narrativo', 'Düz metin', 'Oписание (Текст)');
   		break;
   	case 'Exam':
   		$review = handle_translation('Exam', 'Esame', 'Sınav', 'Изпит');
   		break;
   	case 'Experiment':
   		$review = handle_translation('Experiment', 'Esperimento', 'Deney', 'Експеримент');
   		break;
   	case 'Problem statement':
   		$review = handle_translation('Problem statement', 'Definizione problema', 'Problem ifadesi', 'Описание на проблем');
   		break;
   	case 'Self assessment':
   		$review = handle_translation('Self assessment', 'Autovalutazione', 'Özdeğerlendirme', 'Самооценка');
   		break;
   	case 'Lecture':
   		$review = handle_translation('Lecture', 'Lettura', 'Anlatım', 'Лекция');
   		break;	
		
	// Time
	case '30 minutes':
		$review = handle_translation('30 minutes', '30 minuti', '30 dakika', '30 минути');
		break;
	case '60 minutes':
		$review = handle_translation('60 minutes', '60 minuti', '60 dakika', '60 минути');
		break;	
	case '90 minutes':
		$review = handle_translation('90 minutes', '90 minuti', '90 dakika', '90 минути');
		break;
	case '+120 minutes':
		$review = handle_translation('+120 minutes', '+120 minuti', '+120 dakika', '+120 минутри');
		break;
													
    }
    return $review;
}

?>