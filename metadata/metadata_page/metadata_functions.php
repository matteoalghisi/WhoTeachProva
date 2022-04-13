<?php
global $CFG;
require_once($CFG->dirroot.'/config.php');

//controlla se la categoria è una sottocategoria
//- $id : id della categoria
function is_subcategory($id) 
{	global $DB;
	$sql="SELECT depth,path FROM mdl_course_categories WHERE id='".$id."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$category_depth = $field->depth;
		$category_path = $field->path;
	}
	
	if($category_depth>1)
		return true;
	else
		return false;
}

//restituisce l'id della categoria padre
//- $id: id della sottocategoria
function get_parent_id($id)
{	global $DB;
	$sql="SELECT depth,path FROM mdl_course_categories WHERE id='".$id."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$category_depth = $field->depth;
		$category_path = $field->path;
	}
	
	
	$cat_path = explode('/', $category_path);
	$parent_category_id=$cat_path[1];
	
	return $parent_category_id;
}

//restituisce il nome della categoria padre
//- $id: id della sottocategoria
function get_parent_name($id)
{	global $DB;
	$sql="SELECT depth,path FROM mdl_course_categories WHERE id='".$id."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$category_depth = $field->depth;
		$category_path = $field->path;
	}
	
	
	$cat_path = explode('/', $category_path);
	$parent_category_id=$cat_path[1];
		
	$sql = "SELECT name FROM mdl_course_categories WHERE  id = '".$parent_category_id."'";
	$fields = $DB->get_records_sql($sql);
	foreach($fields as $field) {
		$parent_category_name = $field->name;
	}
	
	return $parent_category_name;
	
}

//restituisce l'id di tutte le sottocategorie
//- $parent_category: l'id della categoria padre
function get_all_categories($parent_category)
{	global $DB;

	$sql="SELECT id,path FROM mdl_course_categories WHERE depth>1";
	$fields = $DB->get_records_sql($sql);
	
	$list_category=array();
	$list_category[0]=$parent_category;	
	$i=1;
	foreach($fields as $field){
		$cat_path = explode('/', $field->path);
		$parent_category_id=$cat_path[1];
			
		if ($parent_category_id==$parent_category){
			$list_category[$i]=$field->id;
			$i++;
		}
	}
	
	//print_r($list_category);
	return $list_category;
	
}

//restituisce il path name completo della categoria
//- $id : l'id della categoria
function get_full_path_name($id)
{	global $DB;

	$sql="SELECT path FROM mdl_course_categories WHERE id='".$id."'";
	$fields = $DB->get_records_sql($sql);
	$cat_path=reset($fields)->path;
	
	$list_id=str_replace("/",",",substr($cat_path, 1, strlen($cat_path)));
	
	$sql="SELECT name FROM mdl_course_categories WHERE id IN (".$list_id.")";
	$fields = $DB->get_records_sql($sql);
	$full_path_name=array();
	foreach($fields as $field){
			array_push($full_path_name, $field->name);
	}
	
	return implode("/",$full_path_name);
	
}


function removeCommonWords($input){
	global $CFG;
	
	$input=strtolower($input);
	
	$commonWords_en = array('a','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');
	//$commonWords_en = explode("\n", file_get_contents($CFG->dirroot.'/metadata/metadata_page/stop_words/stop_word_en.txt'));
	//$commonWords_it = explode("\n", file_get_contents($CFG->dirroot.'/metadata/metadata_page/stop_words/stop_word.txt'));
	$commonWords_it=array('attraverso ','partecipazioni ','azionarie ','iniziativa ','private ','portafoglio ','forte ','canino ','milioni ','euro ','notizie ','analisi ','decreto ','rilancia ','durata ','market ','insight ','reasons ','happen ','enabled ','complete ','banca ','generali ','amministratore ','delegato ','nord ','ovest ','piemonte ','valle ','d\'sosta ','gian ','maria ','mossa ','parters ','capital ','citywire.it ','grandi ','deutsche ','bank ','accesshappen ','completing ','altro ','cookie ','tim ','attraverso ','partecipazioni ','azionarie ','iniziativa ','private ','portafoglio ','forte ','canino ','milioni ','euro ','notizie ','analisi ','decreto ','rilancia ','durata ','anni ','fondazione ','parte ','azimut ','crt ','protagonisti ','market ','night ','intervista ','aggiunto ','secondo ','massimo ','stampa ','scelto ','milano ','banca ','reason ','happen ','citywireit ','amministrazione ','forbidden ','bgenerali ','real ','investireoggiit ','illimity ','villa ','tramite ','guarda ','viviamo ','completing ','javascript ','acces ','citywire ','amministratore ','sanpaolo ','cookie ','foto ','vuoi ','email ','cap ','delegato ','canino ','crt ','enabled ','complete ','milano ','captcha ','notizie ','mondo ','attività ','protagonisti ','private ','person ','reasons ','curata ','giusta ','portando ','contributo ','azionarie ','partecipazioni ','intervento ','parte ','scelto ','potrebbe ','secondo ','massimo ','aggiunto ','inoltre ','quasi ','a ','quello ','vario ','dove ','assai ','miliardo ','gruppo ','volto ','ci ','consecutivo ','riecco ','lungo ','sig ','concernere ','triplo ','ed ','perché ','atteso ','persino ','citta ','cui ','qualcuno ','ben ','parecchio ','ex ','gliene ','recentemente ','anche ','benissimo ','agl ','fino ','dovra ','quindi ','potere ','ossia ','macche ','se ','nuovo ','ti ','mondare ','della ','uguale ','giorno ','mio ','ã¨ ','proprio ','menare ','gente ','quale ','li ','pieno ','nel ','alcuno ','press ','perchè ','può ','quest ','successivamente ','futuro ','titolare ','ciò ','poi ','sopra ','stemmo ','fuori ','luogo ','da ','versare ','rispettare ','fosso ','mila ','tutto ','ansare ','casa ','nullo ','senza ','nemmeno ','nostro ','cittã ','dagl ','troppo ','davanti ','cos ','pochissimo ','posteriore ','neppure ','tempo ','vi ','questo ','bastare ','circa ','spesso ','ma ','quantunque ','realmente ','mezzo ','solere ','soltanto ','quinto ','esempio ','milione ','peggio ','bene ','forse ','fossa ','ie ','su ','anno ','tuttavia ','dell ','al ','sarã ','col ','primo ','voi ','qualche ','consigliare ','poco ','marca ','registrazione ','oppure ','già ','tra ','però ','purtroppo ','variare ','ecc ','mai ','cortesia ','conclusione ','il ','poiche ','nondimeno ','cioã¨ ','sotto ','lã ','vicino ','quanto ','degl ','ciascuno ','chicchessia ','si ','sul ','andare ','ognuna ','promettere ','cioe ','malo ','ora ','negl ','esse ','puro ','dentro ','principalmente ','paese ','finche ','caso ','soprattutto ','città ','scolare ','piccolo ','me ','lasciare ','mediare ','buono ','improvvisare ','così ','qualcuna ','allora ','partire ','talvolta ','nome ','à ','dovere ','niente ','seguitare ','dunque ','alcun ','perchã¨ ','perã² ','c ','di ','dov ','dappertutto ','favore ','seguire ','preferibilmente ','peccare ','sembrare ','cioè ','vaio ','varo ','fin ','piuttosto ','avanti ','colorire ','quarto ','che ','in ','tale ','fece ','d ','nessuno ','i ','ciã² ','lei ','torino ','dall ','io ','del ','successivo ','intanto ','egli ','intorno ','ahimã¨ ','altrove ','dio ','giacca ','qua ','sull ','tranne ','berlusconi ','certo ','ciascun ','piu ','stare ','via ','mese ','giu ','puo ','malissimo ','governare ','setta ','comunque ','comprare ','molto ','secondare ','cimare ','avere ','eppure ','grazia ','piede ','piã¹ ','altro ','nella ','indietro ','suo ','qualunque ','persona ','durare ','dietro ','fare ','uomo ','mosto ','vostro ','dal ','dopo ','ne ','altrui ','recere ','cominciare ','salvare ','codesto ','anticipare ','mancanza ','lato ','adesso ','grande ','frattempo ','ogni ','nonsia ','ahimè ','ultimare ','conciliarsi ','preso ','essi ','nonostante ','l ','là ','terzo ','ulteriore ','co ','dalla ','stato ','cosi ','otto ','momento ','insieme ','lontano ','modo ','ahime ','e ','essere ','sara ','noi ','cio ','ad ','dirimpetto ','dare ','nessun ','cosã¬ ','forzare ','novanta ','neo ','srl ','cè ','pigliare ','po ','qui ','averlo ','nove ','scopare ','abbastanza ','per ','tu ','mi ','diro ','oltre ','gliele ','qualcosa ','come ','quando ','haha ','ottanta ','tangere ','effettivamente ','sugl ','glieli ','osare ','relativo ','percio ','colei ','finalmente ','te ','chi ','due ','altrimenti ','dire ','ognuno ','inc ','perfino ','alla ','colui ','invece ','perche ','dovrã ','trovare ','gliela ','pero ','trenta ','entrambi ','scorrere ','stettero ','fra ','tre ','giã ','attraversare ','quattro ','diventare ','postare ','sempre ','ella ','oggi ','orare ','moltissimo ','perciò ','od ','ancorare ','affinche ','cento ','infatti ','puã² ','contro ','ieri ','lavorare ','mentre ','probabilmente ','sulla ','bravo ','solito ','possedere ','medesimo ','doppiare ','con ','subire ','o ','gia ','maggior ','malgrado ','tuo ','loro ','dovunque ','valere ','vistare ','all ','cosa ','la ','generale ','ministrare ','th ','lui ','stesso ','vita ','glielo ','cinque ','magari ','nazionale ','meglio ','perciã² ','nell ','chiunque ','accidente ','ecco ','a ','abbia ','abbiamo ','abbiano ','abbiate ','ad ','adesso ','agl ','agli ','ai ','al ','all ','alla ','alle ','allo ','allora ','altre ','altri ','altro ','anche ','ancora ','avemmo ','avendo ','avere ','avesse ','avessero ','avessi ','avessimo ','aveste ','avesti ','avete ','aveva ','avevamo ','avevano ','avevate ','avevi ','avevo ','avrai ','avranno ','avrebbe ','avrebbero ','avrei ','avremmo ','avremo ','avreste ','avresti ','avrete ','avrà ','avrò ','avuta ','avute ','avuti ','avuto ','c ','che ','chi ','ci ','coi ','col ','come ','con ','contro ','cui ','da ','dagl ','dagli ','dai ','dal ','dall ','dalla ','dalle ','dallo ','degl ','degli ','dei ','del ','dell ','della ','delle ','dello ','dentro ','di ','dov ','dove ','e ','ebbe ','ebbero ','ebbi ','ecco ','ed ','era ','erano ','eravamo ','eravate ','eri ','ero ','essendo ','faccia ','facciamo ','facciano ','facciate ','faccio ','facemmo ','facendo ','facesse ','facessero ','facessi ','facessimo ','faceste ','facesti ','faceva ','facevamo ','facevano ','facevate ','facevi ','facevo ','fai ','fanno ','farai ','faranno ','fare ','farebbe ','farebbero ','farei ','faremmo ','faremo ','fareste ','faresti ','farete ','farà ','farò ','fece ','fecero ','feci ','fino ','fosse ','fossero ','fossi ','fossimo ','foste ','fosti ','fra ','fu ','fui ','fummo ','furono ','giù ','gli ','ha ','hai ','hanno ','ho ','i ','il ','in ','io ','l ','la ','le ','lei ','li ','lo ','loro ','lui ','ma ','me ','mi ','mia ','mie ','miei ','mio ','ne ','negl ','negli ','nei ','nel ','nell ','nella ','nelle ','nello ','no ','noi ','non ','nostra ','nostre ','nostri ','nostro ','o ','per ','perché ','però ','più ','pochi ','poco ','qua ','quale ','quanta ','quante ','quanti ','quanto ','quasi ','quella ','quelle ','quelli ','quello ','questa ','queste ','questi ','questo ','qui ','quindi ','sarai ','saranno ','sarebbe ','sarebbero ','sarei ','saremmo ','saremo ','sareste ','saresti ','sarete ','sarà ','sarò ','se ','sei ','senza ','si ','sia ','siamo ','siano ','siate ','siete ','sono ','sopra ','sotto ','sta ','stai ','stando ','stanno ','starai ','staranno ','stare ','starebbe ','starebbero ','starei ','staremmo ','staremo ','stareste ','staresti ','starete ','starà ','starò ','stava ','stavamo ','stavano ','stavate ','stavi ','stavo ','stemmo ','stesse ','stessero ','stessi ','stessimo ','stesso ','steste ','stesti ','stette ','stettero ','stetti ','stia ','stiamo ','stiano ','stiate ','sto ','su ','sua ','sue ','sugl ','sugli ','sui ','sul ','sull ','sulla ','sulle ','sullo ','suo ','suoi ','te ','ti ','tra ','tu ','tua ','tue ','tuo ','tuoi ','tutti ','tutto ','un ','una ','uno ','vai ','vi ','voi ','vostra ','vostre ','vostri ','vostro ','è ','banca ','generali ','mediolanum ','cliente ','clienti ','né ','dati ','fineco ','banche ','cio ','possono ','viene ','solo ','seguente ','che ','deve ','di ','una','attraverso ','partecipazioni ','azionarie ','iniziativa ','private ','portafoglio ','forte ','canino ','milioni ','euro ','notizie ','analisi ','decreto ','rilancia ','durata ','market ','insight ','reasons ','happen ','enabled ','complete ','banca ','generali ','amministratore ','delegato ','nord ','ovest ','piemonte ','valle ','d\'sosta ','gian ','maria ','mossa ','parters ','capital ','citywire.it ','grandi ','deutsche ','bank ','accesshappen ','completing ','altro ','cookie ','tim ','attraverso ','partecipazioni ','azionarie ','iniziativa ','private ','portafoglio ','forte ','canino ','milioni ','euro ','notizie ','analisi ','decreto ','rilancia ','durata ','anni ','fondazione ','parte ','azimut ','crt ','protagonisti ','market ','night ','intervista ','aggiunto ','secondo ','massimo ','stampa ','scelto ','milano ','banca ','reason ','happen ','citywireit ','amministrazione ','forbidden ','bgenerali ','real ','investireoggiit ','illimity ','villa ','tramite ','guarda ','viviamo ','completing ','javascript ','acces ','citywire ','amministratore ','sanpaolo ','cookie ','foto ','vuoi ','email ','cap ','delegato ','canino ','crt ','enabled ','complete ','milano ','captcha ','notizie ','mondo ','attività ','protagonisti ','private ','person ','reasons ','curata ','giusta ','portando ','contributo ','azionarie ','partecipazioni ','intervento ','parte ','scelto ','potrebbe ','secondo ','massimo ','aggiunto ','inoltre ','quasi ','a ','quello ','vario ','dove ','assai ','miliardo ','gruppo ','volto ','ci ','consecutivo ','riecco ','lungo ','sig ','concernere ','triplo ','ed ','perché ','atteso ','persino ','citta ','cui ','qualcuno ','ben ','parecchio ','ex ','gliene ','recentemente ','anche ','benissimo ','agl ','fino ','dovra ','quindi ','potere ','ossia ','macche ','se ','nuovo ','ti ','mondare ','della ','uguale ','giorno ','mio ','ã¨ ','proprio ','menare ','gente ','quale ','li ','pieno ','nel ','alcuno ','press ','perchè ','può ','quest ','successivamente ','futuro ','titolare ','ciò ','poi ','sopra ','stemmo ','fuori ','luogo ','da ','versare ','rispettare ','fosso ','mila ','tutto ','ansare ','casa ','nullo ','senza ','nemmeno ','nostro ','cittã ','dagl ','troppo ','davanti ','cos ','pochissimo ','posteriore ','neppure ','tempo ','vi ','questo ','bastare ','circa ','spesso ','ma ','quantunque ','realmente ','mezzo ','solere ','soltanto ','quinto ','esempio ','milione ','peggio ','bene ','forse ','fossa ','ie ','su ','anno ','tuttavia ','dell ','al ','sarã ','col ','primo ','voi ','qualche ','consigliare ','poco ','marca ','registrazione ','oppure ','già ','tra ','però ','purtroppo ','variare ','ecc ','mai ','cortesia ','conclusione ','il ','poiche ','nondimeno ','cioã¨ ','sotto ','lã ','vicino ','quanto ','degl ','ciascuno ','chicchessia ','si ','sul ','andare ','ognuna ','promettere ','cioe ','malo ','ora ','negl ','esse ','puro ','dentro ','principalmente ','paese ','finche ','caso ','soprattutto ','città ','scolare ','piccolo ','me ','lasciare ','mediare ','buono ','improvvisare ','così ','qualcuna ','allora ','partire ','talvolta ','nome ','à ','dovere ','niente ','seguitare ','dunque ','alcun ','perchã¨ ','perã² ','c ','di ','dov ','dappertutto ','favore ','seguire ','preferibilmente ','peccare ','sembrare ','cioè ','vaio ','varo ','fin ','piuttosto ','avanti ','colorire ','quarto ','che ','in ','tale ','fece ','d ','nessuno ','i ','ciã² ','lei ','torino ','dall ','io ','del ','successivo ','intanto ','egli ','intorno ','ahimã¨ ','altrove ','dio ','giacca ','qua ','sull ','tranne ','berlusconi ','certo ','ciascun ','piu ','stare ','via ','mese ','giu ','puo ','malissimo ','governare ','setta ','comunque ','comprare ','molto ','secondare ','cimare ','avere ','eppure ','grazia ','piede ','piã¹ ','altro ','nella ','indietro ','suo ','qualunque ','persona ','durare ','dietro ','fare ','uomo ','mosto ','vostro ','dal ','dopo ','ne ','altrui ','recere ','cominciare ','salvare ','codesto ','anticipare ','mancanza ','lato ','adesso ','grande ','frattempo ','ogni ','nonsia ','ahimè ','ultimare ','conciliarsi ','preso ','essi ','nonostante ','l ','là ','terzo ','ulteriore ','co ','dalla ','stato ','cosi ','otto ','momento ','insieme ','lontano ','modo ','ahime ','e ','essere ','sara ','noi ','cio ','ad ','dirimpetto ','dare ','nessun ','cosã¬ ','forzare ','novanta ','neo ','srl ','cè ','pigliare ','po ','qui ','averlo ','nove ','scopare ','abbastanza ','per ','tu ','mi ','diro ','oltre ','gliele ','qualcosa ','come ','quando ','haha ','ottanta ','tangere ','effettivamente ','sugl ','glieli ','osare ','relativo ','percio ','colei ','finalmente ','te ','chi ','due ','altrimenti ','dire ','ognuno ','inc ','perfino ','alla ','colui ','invece ','perche ','dovrã ','trovare ','gliela ','pero ','trenta ','entrambi ','scorrere ','stettero ','fra ','tre ','giã ','attraversare ','quattro ','diventare ','postare ','sempre ','ella ','oggi ','orare ','moltissimo ','perciò ','od ','ancorare ','affinche ','cento ','infatti ','puã² ','contro ','ieri ','lavorare ','mentre ','probabilmente ','sulla ','bravo ','solito ','possedere ','medesimo ','doppiare ','con ','subire ','o ','gia ','maggior ','malgrado ','tuo ','loro ','dovunque ','valere ','vistare ','all ','cosa ','la ','generale ','ministrare ','th ','lui ','stesso ','vita ','glielo ','cinque ','magari ','nazionale ','meglio ','perciã² ','nell ','chiunque ','accidente ','ecco ','a ','abbia ','abbiamo ','abbiano ','abbiate ','ad ','adesso ','agl ','agli ','ai ','al ','all ','alla ','alle ','allo ','allora ','altre ','altri ','altro ','anche ','ancora ','avemmo ','avendo ','avere ','avesse ','avessero ','avessi ','avessimo ','aveste ','avesti ','avete ','aveva ','avevamo ','avevano ','avevate ','avevi ','avevo ','avrai ','avranno ','avrebbe ','avrebbero ','avrei ','avremmo ','avremo ','avreste ','avresti ','avrete ','avrà ','avrò ','avuta ','avute ','avuti ','avuto ','c ','che ','chi ','ci ','coi ','col ','come ','con ','contro ','cui ','da ','dagl ','dagli ','dai ','dal ','dall ','dalla ','dalle ','dallo ','degl ','degli ','dei ','del ','dell ','della ','delle ','dello ','dentro ','di ','dov ','dove ','e ','ebbe ','ebbero ','ebbi ','ecco ','ed ','era ','erano ','eravamo ','eravate ','eri ','ero ','essendo ','faccia ','facciamo ','facciano ','facciate ','faccio ','facemmo ','facendo ','facesse ','facessero ','facessi ','facessimo ','faceste ','facesti ','faceva ','facevamo ','facevano ','facevate ','facevi ','facevo ','fai ','fanno ','farai ','faranno ','fare ','farebbe ','farebbero ','farei ','faremmo ','faremo ','fareste ','faresti ','farete ','farà ','farò ','fece ','fecero ','feci ','fino ','fosse ','fossero ','fossi ','fossimo ','foste ','fosti ','fra ','fu ','fui ','fummo ','furono ','giù ','gli ','ha ','hai ','hanno ','ho ','i ','il ','in ','io ','l ','la ','le ','lei ','li ','lo ','loro ','lui ','ma ','me ','mi ','mia ','mie ','miei ','mio ','ne ','negl ','negli ','nei ','nel ','nell ','nella ','nelle ','nello ','no ','noi ','non ','nostra ','nostre ','nostri ','nostro ','o ','per ','perché ','però ','più ','pochi ','poco ','qua ','quale ','quanta ','quante ','quanti ','quanto ','quasi ','quella ','quelle ','quelli ','quello ','questa ','queste ','questi ','questo ','qui ','quindi ','sarai ','saranno ','sarebbe ','sarebbero ','sarei ','saremmo ','saremo ','sareste ','saresti ','sarete ','sarà ','sarò ','se ','sei ','senza ','si ','sia ','siamo ','siano ','siate ','siete ','sono ','sopra ','sotto ','sta ','stai ','stando ','stanno ','starai ','staranno ','stare ','starebbe ','starebbero ','starei ','staremmo ','staremo ','stareste ','staresti ','starete ','starà ','starò ','stava ','stavamo ','stavano ','stavate ','stavi ','stavo ','stemmo ','stesse ','stessero ','stessi ','stessimo ','stesso ','steste ','stesti ','stette ','stettero ','stetti ','stia ','stiamo ','stiano ','stiate ','sto ','su ','sua ','sue ','sugl ','sugli ','sui ','sul ','sull ','sulla ','sulle ','sullo ','suo ','suoi ','te ','ti ','tra ','tu ','tua ','tue ','tuo ','tuoi ','tutti ','tutto ','un ','una ','uno ','vai ','vi ','voi ','vostra ','vostre ','vostri ','vostro ','è ','banca ','generali ','mediolanum ','cliente ','clienti ','né ','dati ','fineco ','banche ','cio ','possono ','viene ','solo ','seguente ','che ','deve ','di ','una');
	
	
	$commonWords=array_merge($commonWords_en,$commonWords_it);
	$temp_string = preg_replace('/\b('.implode('|',$commonWords).')\b/','',$input);
	$temp_string = preg_replace('/[^A-Za-z0-9\- ]/', '', $temp_string);
	$str = strtolower($temp_string);

	$token = strtok($str, " ");
	$tokens = [];
  
	while ($token !== false)
	{
	//echo "$token<br>";
	array_push($tokens,$token);
	$token = strtok(" ");
	}
	return $tokens;
}


//Funzione per estrarre le keywords dalle risorse
//- $moduleID -> id del tipo risorsa
//- $parent_course -> id del corso di appartenenza
//- $parent_section -> id della sezione di appartenenza
//- $id_resource -> id della risorsa
//- $courseGrade -> grado di valutazione del corso
//- $falg -> se è settato a 2: la risorsa è in fase di creazione
//			 se è settato a 1: la risorsa è in fase di modifica
function extract_keywords($moduleID,$parent_course, $parent_section, $id_resource,$courseGrade, $flag){
	global $DB,$CFG;
	
	//gestione risorsa file
	if($moduleID==17){
		
		//al momento sono gestiti solamente i pdf
		$sql="SELECT * from mdl_files WHERE contextid=(SELECT id from mdl_context where contextlevel=70 and instanceid='".$id_resource."' AND mimetype='application/pdf' AND filearea='content')";
		$fields = $DB->get_records_sql($sql);
		
		//foreach per tutti i pdf associati alla risorsa
		foreach ($fields as $field){
			$request_parameters=array();
			$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($CFG->dataroot.'\\filedir'));
			
			//trova il path assoluto del pdf e crea l'array dei parametri
			foreach ($it as $file) {
				if($file->getFilename()==$field->contenthash){
					$fullpath = $file->getPath().'\\'.$file->getFilename() ;
					$request_parameters=array('resourcetype' => '17','resourceid' => $id_resource,'file'=> new CURLFILE($fullpath),'filetype' => 'application/pdf');
					//print_r($request_parameters);
				}
			}
			
			//chiama la funzione send_request
			if($keywords_list=send_request($request_parameters)){
				$old_keywords=array();
				$sql="SELECT DISTINCT value FROM mdl_metadata WHERE id_course=$parent_course AND id_course_sections=$parent_section AND id_resource=$id_resource AND property='keywords'";
				$old_keywords=array_keys($DB->get_records_sql($sql));
				//print_r($old_keywords);
				
				//salva le keyowords ignorando quelle duplicate
				foreach($keywords_list as $keyword){
					if(!in_array($keyword, $old_keywords)){
						$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'keywords', '".$keyword."', $courseGrade)";
						$DB->execute($sql);
						}
					}
				}
			}
	}
	
	//gestione risorsa quiz: manda la request solo durante la fase di modifica
	else if($moduleID==16 && $flag==1){
		
		//crea l'array dei parametri
		$request_parameters=array('resourcetype' => '16','resourceid' => $id_resource);
		
		//chiama la funzione send_request
		if($keywords_list=send_request($request_parameters)){
		
		$old_keywords=array();
		$sql="SELECT DISTINCT value FROM mdl_metadata WHERE id_course=$parent_course AND id_course_sections=$parent_section AND id_resource=$id_resource AND property='keywords'";
		$old_keywords=array_keys($DB->get_records_sql($sql));

			//salva le keyowords ignorando quelle duplicate
			foreach($keywords_list as $keyword){
				if(!in_array($keyword, $old_keywords)){
					$sql="INSERT INTO mdl_metadata(id_course, id_course_sections, id_resource, property, value, courseGrade) VALUES ($parent_course, $parent_section, $id_resource, 'keywords', '".$keyword."', $courseGrade)";
					$DB->execute($sql);
				}
			}
		}
	}
	
}

//funzione che manda la request al keywordsServer
//-$request_parameters -> l'array dei parametri da inviare
function send_request($request_parameters){
	global $DB;
	$curl = curl_init();
	
	curl_setopt_array($curl, array(
								CURLOPT_URL => "51.136.40.203:5003",
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "POST",
								CURLOPT_POSTFIELDS => $request_parameters,
								CURLOPT_HTTPHEADER => array("X-My-App-Auth-Token: utea3Quaaxohh1Oo"),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	$decoded_response=json_decode($response);
	
	print_r($decoded_response);
	if($decoded_response->response=='successful'){
		$decoded_response->keywords=str_replace(array('[', ']','"', ' '),'',$decoded_response->keywords);
		$keywords_list = explode(',', $decoded_response->keywords);
		return $keywords_list;
	}
	else
		return false;
	
	
}


?>