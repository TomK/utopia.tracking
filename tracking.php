<?php

class tabledef_Tracking extends uTableDef {
	public $tablename = 'tracking';

	public function SetupFields() {
		// add all the fields in here
		// AddField($name, $type, $length, $collation='', $attributes='', $null='not null', $default='', $extra='', $comments='')
		// SetPrimaryKey($name);

		$this->AddField('tracking_id',ftNUMBER);
		$this->AddInputDate('input_date');
		$this->AddField('url',ftVARCHAR,255);
		$this->AddField('user_agent',ftVARCHAR,255);
		$this->AddField('referrer',ftVARCHAR,255);
		$this->AddField('ip',ftVARCHAR,15);

		$this->SetPrimaryKey('tracking_id');
		$this->SetIndexField('input_date');
	}
}

class module_Tracking extends uListDataModule {
	// title: the title of this page, to appear in header box and navigation
	public function GetTitle() { return 'Tracking'; }
	public function GetOptions() { return IS_ADMIN | ALLOW_FILTER; }
	public function GetTabledef() { return 'tabledef_Tracking'; }

	public function SetupParents() {
		$this->RegisterAjax('track',array($this,'track'));
		$this->AddParent('internalmodule_Admin');

		uJavascript::IncludeText('$.ajax({type:\'post\',data:{"__ajax":"track","r":document.referrer}});');
	}

	public function SetupFields() {
		$this->CreateTable('tracking');

		$this->AddField('input_date',"input_date",'tracking','Input');
		//$this->AddField('url',"<a target=\"_blank\" href=\"{url}\">{url}</a>",'tracking','URL');
		$this->AddField('url','url','tracking','URL');
		$this->AddField('referrer','referrer','tracking','Referrer');
		$this->AddField('user_agent',"user_agent",'tracking','Agent');
		$this->AddField('ip',"ip",'tracking','IP Address');
	}

	public function track() {
		ignore_user_abort(true);

		// if referrer is not from current domain then break;
		$url = $_SERVER['HTTP_REFERER'];
		if (strpos($url,'://'.utopia::GetDomainName()) === FALSE) return;
		
		$obj = utopia::GetInstance(GetCurrentModule());
		if (flag_is_set($obj->GetOptions(),IS_ADMIN)) return;

		$this->UpdateFields(array(
			'url'=>$url,
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'referrer'=>$_POST['r'],
			'ip'=>$_SERVER['REMOTE_ADDR']
		));
	}

	public function RunModule() {
		$this->ShowData();
	}
}

?>
