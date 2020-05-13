<?php

namespace Model\Email;

use Model\Email\PHPMailer;
use Model\Email\SMTP;

class Email{

	protected $_configura = array();

	public $acentos = array(
			'Á' => '&Aacute;',
			'á' => '&aacute;',
			'Â' => '&Acirc;',
			'â' => '&acirc;',
			'À' => '&Agrave;',
			'à' => '&agrave;',
			'Å' => '&Aring;',
			'å' => '&aring;',
			'Ã' => '&Atilde;',
			'ã' => '&atilde;',
			'Ä' => '&Auml;',
			'ä' => '&auml;',
			'Æ' => '&AElig;',
			'æ' => '&aelig;',
			'É' => '&Eacute;',
			'é' => '&eacute;',
			'Ê' => '&Ecirc;',
			'ê' => '&ecirc;',
			'È' => '&Egrave;',
			'è' => '&egrave;',
			'Ë' => '&Euml;',
			'ë' => '&euml;',

			'Í' => '&Iacute;',
			'í' => '&iacute;',
			'Î' => '&Icirc;',
			'î' => '&icirc;',
			'Ì' => '&Igrave;',
			'ì' => '&igrave;',
			'Ï' => '&Iuml;',
			'ï' => '&iuml;',
			'Ó' => '&Oacute;',
			'ó' => '&oacute;',
			'Ô' => '&Ocirc;',
			'ô' => '&ocirc;',
			'Ò' => '&Ograve;',
			'ò' => '&ograve;',
			'Ø' => '&Oslash;',
			'ø' => '&oslash;',
			'Õ' => '&Otilde;',
			'õ' => '&otilde;',
			'Ö' => '&Ouml;',
			'ö' => '&ouml;',

			'Ú' => '&Uacute;',
			'ú' => '&uacute;',
			'Û' => '&Ucirc;',
			'û' => '&ucirc;',
			'Ù' => '&Ugrave;',
			'ù' => '&ugrave;',
			'Ü' => '&Uuml;',
			'ü' => '&uuml;',
			'Ç' => '&Ccedil;',
			'ç' => '&ccedil;',
			'Ñ' => '&Ntilde;',
			'ñ' => '&ntilde;',

			/*'<' => '&lt;',
			'>' => '&gt;',
			'&' => '&amp;',
			'"' => '&quot;',
			'®' => '&reg;',
			'©' => '&copy;',
			'Ý' => '&Yacute;',
			'ý' => '&yacute;',
			'Þ' => '&THORN;',
			'þ' => '&thorn;',
			'ß' => '&szlig;'*/
		);

	function __construct($configura){
		$this->_configura = $configura;
	}

	function enviar($envia){

		try{

			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->IsHTML(true);
			$mail->Charset 		= 'UTF-8';
			$mail->SMTPAuth 	= true;
			$mail->SMTPDebug 	= $this->_configura['debug'];
			$mail->SMTPSecure 	= $this->_configura['crip'];
			$mail->Host 		= $this->_configura['host'];
			$mail->Port 		= $this->_configura['port'];
			$mail->Username 	= $this->_configura["user"];
			$mail->Password 	= $this->_configura["pass"];
			$mail->Subject 		= $envia['assunto'];

			// SUBSTITUI ACENTOS POR CARACTERES ESPECIAIS HTML
			$acentoKey = array_keys($this->acentos);
			$acentoVal = array_values($this->acentos);
			$enviabody = str_replace($acentoKey, $acentoVal, $envia['body']);

			// QUEM ESTÁ ENVIANDO
			$mail->SetFrom($envia['from'], $envia['nome']);

			// RESPONDER PARA
			if(isset($envia['responderPara'], $envia['responderPara']['email'], $envia['responderPara']['nome'])){
				$mail->ClearReplyTos();
				$mail->AddReplyTo($envia['responderPara']['email'], utf8_decode($envia['responderPara']['nome']));
			}

			// ENVIA COMO CÓPIA
			if(isset($envia['comoCopia']) and is_array($envia['comoCopia'])){

				foreach($envia['comoCopia'] as $a => $b){
					$mail->AddCC($a, $b);
				}
			}

			// ENVIA COMO CÓPIA OCULTA
			if(isset($envia['comoCopiaOculta']) and is_array($envia['comoCopiaOculta'])){

				foreach($envia['comoCopiaOculta'] as $a => $b){
					$mail->AddBCC($a, $b);
				}
			}

			// DESTINATÁRIOS
			if(isset($envia['para']) and is_array($envia['para'])){

				$key = 0;
				foreach($envia['para'] as $a => $b){


					$mail->AddAddress($a, utf8_decode($b));

					if($key == 0){

						//$mail->AddCustomHeader('List-Unsubscribe', '<'.SITE_GESTOR.'/unsubscribe?i=1&e='.$a.'>,<mailto:'.$envia['from'].'?subject='.$a.'>');

						//$enviabody = str_replace('</body>', '<p style="text-align: center;"><a style="font-size: 9px;" href="'.SITE_GESTOR.'/unsubscribe?i=1&e='.$a.'">Clique aqui para não receber mais emails</a></p></body>', $enviabody);
					}

					$key++;
				}

			}else{

				//$mail->AddCustomHeader('List-Unsubscribe', '<'.SITE_GESTOR.'/unsubscribe?i=1&e='.$envia['para'].'>,<mailto:'.$envia['from'].'?subject='.$envia['para'].'>');

				$mail->AddAddress($envia['para'], '');
			}

			$mail->Body = $enviabody;

			// ENVIA ANEXO
			if(isset($envia['anexo'], $envia['anexo']['pasta'], $envia['anexo']['nome']) and is_file($envia['anexo']['pasta'])){
				$mail->AddAttachment($envia['anexo']['pasta'], $envia['anexo']['nome']);
			}

			// PROBLEMAS AO ENVIAR
			if(!$mail->Send()){

				return $mail->ErrorInfo;

			// ENVIADO COM SUCESSO
			}else{

				return true;

			}

		}catch(Exception $e){
				
			return $e;

		}
	}
}