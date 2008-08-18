<?php
/**************************************************************************
	PHP Api Lib
	Copyright (C) 2007  Kw4h

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
**************************************************************************/
class Charactersheet
{	
	static function getSkillInTraining($contents)
	{		
		if (!empty($contents))
		{
			$xml = new SimpleXMLElement($contents);
			
			$output = array();
			foreach ($xml->result->children() as $name => $value)
			{
				$output[(string) $name] = (string) $value;
			}
			
			return $output;
		}
		
		return null;
	}
	
	static function getCharacterSheet($contents)
	{		
		if (!empty($contents))
		{
			$xml = new SimpleXMLElement($contents);
			
			$output = array();
			
			// get the general info of the char
			$output['info'] = array();
			foreach ($xml->result->children() as $name => $value)
			{
				$newval = (string) $value;
				
				if (!empty($newval))
				{
					$output['info'][(string) $name] = (string) $value;
				}
			}
			
			// get the attributeEnhancers of the char
			$output['enhancers'] = array();
			foreach ($xml->result->attributeEnhancers as $attribute)
			{				
				foreach ($attribute->children() as $name => $value)
				{
					$output['enhancers'][(string) $name] = array();
					
					foreach ($value->children() as $key => $val)
					{
						$output['enhancers'][(string) $name][(string) $key] = (string) $val;
					}
				}
			}
			
			// get the attributes of the char
			$output['attributes'] = array();
			foreach ($xml->result->attributes->children() as $name => $value)
			{
				$output['attributes'][(string) $name] = (int) $value;
			}
			
			// get the actual skills
			$output['skills'] = array();
			foreach ($xml->result->rowset->row as $row)
			{
				$index = count($output['skills']);
				foreach ($row->attributes() as $name => $value)
				{
					$output['skills'][$index][(string) $name] = (int) $value;
				}
			}
			
			return $output;
		}
		
		return null;
	}
}
?>