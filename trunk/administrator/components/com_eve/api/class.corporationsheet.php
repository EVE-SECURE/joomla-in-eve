<?php

class CorporationSheet {
	 static function getCorporationSheet($content) {
	 	
		if (!empty($content)) {
			$xml = new SimpleXMLElement($content);
			
			$output = array();
			
			foreach ($xml->result->children() as $name => $value) {
				$newval = (string) $value;
				if (!empty($newval)) {
					$output[(string) $name] = (string) $value;
				}	
			}
			
			$divisions = array('divisions', 'walletDivisions');
			foreach($divisions as $division) {
				$output[$division] = array();
				$xdivisions = $xml->xpath('/eveapi/result/rowset[@name="'.$division.'"]/row');
				
				if (empty($xdivisions)) {
					continue;
				}
				
				foreach ($xdivisions as $xdiv) {
					$attribs = $xdiv->attributes();
					$output[$division][(string) $attribs->accountKey] = (string) $attribs->description; 
				}
			}
			
			$output['logo'] = array();
			
			foreach ($xml->result->logo->children() as $name => $value) {
				$newval = (string) $value;
				
				if (!empty($newval)) {
					$output['logo'][(string) $name] = (string) $value;
				}	
			}
			return $output;
		}
		return null;
	 }
}

?>