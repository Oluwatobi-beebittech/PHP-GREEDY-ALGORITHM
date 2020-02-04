<?php
	
	/*
		Program Details: Detects similarity between two strings using the GreedyString
						 tiling algorithm in PHP
		Author in PHP: Akanji, Oluwatobi Shadrach
		Contact: akanjioluwatobishadrach@yahoo.com


		Original Author: Chris Schiffhauer (implementing greedy string tiling
						  algorithm using C# (.NET))
		Website: www.schiffhauer.com/implementing-greedy-string-tiling-in-c/
	*/
	
	//Match class

	class Match
	{

		public $patternPosition;
		public $textPosition;
		public $length;
		public $value;

		/*
			Constructor
			Takes pattern position, text position, minimum match length and value
		*/

		function __construct($patternPos, $textPos, $length, $value)
		{

			$this->patternPosition = $patternPos;
			$this->textPosition = $textPos;
			$this->length = $length;
			$this->value = $value;
		}

		public function getPatternPos()
		{
			return $this->patternPosition;
		}
		public function getTextPos()
		{
			return $this->textPosition;
		}
		public function getLength()
		{
			return $this->length;
		}
		public function getValue()
		{
			return $this->value;
		}

	}
	#end of Match class

	//Token class

	class Token
	{

		public $tokenItems = array();	//an array that takes values of the object TokenItem

		function __construct($content)
		{
			#for each item in content, we add a token item to
			#the token items array
			$content2 = str_split($content);
			foreach ($content2 as $value) 
			{
				array_push($this->tokenItems, new TokenItem($value));
			}
			
		}

		public function getTokenItem()
		{
			return $this->tokenItems;
		}
		public function setTokenItem($val)
		{
			$this->tokenItems = $val;
		}
	}
	#end of Token class

	//TokenItem Class
	class TokenItem
	{
		public $Content;
		public $isMarked;

		function __construct($content)
		{
			$this->Content = $content;
			$this->isMarked = false;
		}

		public function getContent()
		{
			return $this->Content;
		}
		public function getIsMarked()
		{
			return $this->isMarked;
		}
		public function setContent($val)
		{
			$this->Content = $val;
		}
		public function setIsMarked($val)
		{
			$this->isMarked = $val;
		}
	}

	/*
		@args
			pattern of type Token
			text of type Token
			minimumMatchingLength of type Token

	*/
	function GreedyStringTiling($pattern, $text, $minMatchLen)
	{
		
		$tiles = array();
		$maxMatch = $minMatchLen;
		$matches = array();

		$patternCount = count($pattern->getTokenItem());
		$textCount = count($text->getTokenItem());
		for ($i = 0; $i < $patternCount; $i++) 
		{ 
			
			for($j = 0; $j < $textCount; $j++)
			{

				$count = 0;
				$check = false;

				while (!$check && ($pattern->getTokenItem()[$i + $count]->getContent()) == ($text->getTokenItem()[$j + $count]->getContent()))
				{

					if(($text->getTokenItem()[$j]->getIsMarked()) && ($text->getTokenItem()[$i]->getIsMarked()))
					{	
						break;
					}

						$count++;

					if(($count + $i) > ($patternCount - 1))
					{
						$check = true;
					}else
					{
						$val = true;
						$pattern->getTokenItem()[$count + $i]->setIsMarked($val);
					}

					if(($count + $j) > ($textCount - 1))
					{
						$check = true;
					}else
					{
						$val = true;
						$text->getTokenItem()[$count + $j]->setIsMarked($val);
					}

				}#end of while
				
				if($count == $maxMatch)
				{
					$value = null;

					for ($z=0; $z < $count; $z++) 
					{ 
						$value .= (String)$pattern->getTokenItem()[$i + $z]->getContent();
					}

					/*
						last text index; getLast Text Value, compare it with currrent value
						string using the substr_count() function
						snippet of code optimization to remove substring detection property
						of an already detected string
					*/

					$match_last_obj = array();
					$last_text_val = "";
					array_push($match_last_obj, end($matches));
					
					if($match_last_obj[0] != "")
					{
						
						$last_text_val = $match_last_obj[0]->getValue();

					}
					
					if($matches == null)
					{
						array_push($matches, new Match($i, $j, $count, $value));
					}
					#end of snippet of code

				}else if($count > $maxMatch)
				{

					$value = null;

					for ($z=0; $z < $count; $z++) 
					{ 
						$value .= (String)$pattern->getTokenItem()[$i + $z]->getContent();
					
					}

					/*
						last text index; getLast Text Value, compare it with currrent value
						string using the substr_count() function
						snippet of code optimization to remove substring detection property
						of an already detected string
					*/

					$match_last_obj = array();
					$last_text_val = "";
					array_push($match_last_obj, end($matches));
					
					if($match_last_obj[0] != "")
					{
					
						$last_text_val = $match_last_obj[0]->getValue();

						if($matches != null)
						{
							if(substr_count(end($matches)->getValue(), $value) == 1)
							{
								#do nothing
							}else
							{
								array_push($matches, new Match($i, $j, $count, $value));
							}
						}
					}
					
					if($matches == null)
					{
						array_push($matches, new Match($i, $j, $count, $value));
					}

					#end of snippet of code
				}

			}#end of inner for...loop

		}#end of outer for...loop

		foreach ($matches as $value) 
		{
			for ($a=0; $a < $maxMatch - 1; $a++) 
			{ 
				$val = true;
				$pattern->getTokenItem()[$value->getPatternPos() + $a]->setIsMarked($val);
				$text->getTokenItem()[$value->getTextPos() + $a]->setIsMarked($val);
			}

			array_push($tiles, $value);

		}#end of foreach loop
		
		return $tiles;
	}

	/*
		Main function to call so as to perfom plagiarism test.
		@args:
			$pattern - pattern string
			$text - text string
			$minMatch[optional] - number of minimum characters to examine for a match.
								Default of five due to average number of characters in English words.

	*/

	function calcPlagiarism($pattern, $text, $minMatch = 5)
	{

		$tmp_str = "";

		if(strlen($pattern) > strlen($text))
		{
			$tmp_str = $text;
			$text = $pattern;
			$pattern = $tmp_str;
		}

		$matches = GreedyStringTiling(new Token($pattern), new Token($text), $minMatch);
		$totalLength = 0;
		foreach ($matches as $key => $value) 
		{
			$totalLength += $matches[$key]->getLength();
			
		}

		$compareLength = 0;
		if(strlen($pattern) > strlen($text))
		{
			$compareLength = strlen($text);
		}else
		{
			$compareLength = strlen($pattern);
		}
		
		return ($totalLength/$compareLength)*100;
	}
     

?>