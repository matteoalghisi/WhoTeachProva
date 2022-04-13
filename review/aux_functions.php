<?php

function handle_translation($arg_en, $arg_it, $arg_tr, $arg_bg) 
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

function translate_review_element($review) {
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
		
	// Category 5: Technical Skills
	case 'Computer Skills':
		$review = handle_translation('Computer Skills', 'Abilità Informatiche', 'Bilgisayar Becerileri', 'Компютърни умения');
		break;	
	case 'IT Basics':
		$review = handle_translation('IT Basics', 'Fondamenti di Tecnologie dell\'Informazione', 'Bilişimin Temelleri', 'Основни ИТ умения');
		break;
	case 'IT Applications Basics':
		$review = handle_translation('IT Applications Basics', 'Applicazioni Informatiche di Base', 'Bilişim Uygulamalarının Temelleri', 'Основни умения за ИТ приложения');
		break;
	case 'Electronic System Tools Basics':
		$review = handle_translation('Electronic System Tools Basics', 'Fondamenti di Strumenti Elettronici', 'Elektronik Sistem Araçlarının Temelleri', 'Основни умения за работа с инструментите на електонни системи');
		break;
	case 'Graphical editor':
		$review = handle_translation('Graphical editor', 'SW di Grafica', 'Çizim SW', 'Графичен редактор');
		break;
	case 'Calculation SW':
		$review = handle_translation('Calculation SW', 'SW di Calcolo', 'Hesaplama SW', 'Софтуер за изчисляван');
		break;
	case 'Project Management SW':
		$review = handle_translation('Project Management SW', 'SW di Gestione di Progetto', 'Proje Yönetimi SW', 'Софтуер за управление на проекти');
		break;
	case 'Document Management SW':
		$review = handle_translation('Document Management SW', 'SW di Gestione di Documenti', 'Belge Yönetimi SW', 'Софтуер за управление на документни потоци');
		break;
	case 'Planning and Control SW':
		$review = handle_translation('Planning and Control SW', 'SW di Pianificazione e Controllo', 'Planlama ve Kontrol SW', 'Софтуер за планиране и контрол');
		break;
	case 'Simulation SW':
		$review = handle_translation('Simulation SW', 'SW di Simulazione', 'Simulasyon SW', 'Симулационен софтуер');
		break;
	case 'Accounting SW':
		$review = handle_translation('Accounting SW', 'SW di Contabilità', 'Muhasebe SW', 'Счетоводен софтуер');
		break;
	case 'Communication SW':
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

function convert_metadata($metadata)
{
    switch ($metadata) {
    	case 'language':
        	$metadata = handle_translation("Language", "Lingua", "Dil", "език");
        	break;
    	case 'keywords':
        	$metadata = handle_translation("Keywords", "Keywords", "Anahtar kelime", "Kлючови думи");
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
        	$metadata = handle_translation("Minimal Age", "Età minima", "Asgari yaş", "Минимална възраст");
        	break;
	case 'max_age':
        	$metadata = handle_translation("Maximal Age", "Età Massima", "Maksimum yaş", "Максимална възраст");
        	break;
    	case 'difficulty':
        	$metadata = handle_translation("Difficulty", "Difficoltà", "Zorluk derecesi", "Cложност");
        	break;
    	case 'time':
        	$metadata = handle_translation("Typical Learning Time", "Tempo d'Apprendimendo", "Süre", "време");
        	break;
    	case 'category':
        	$metadata = handle_translation("Category", "Categoria", "Kategori", "Kатегория");
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
    }

    return $metadata;
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

?>
