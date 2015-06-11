<?php
	namespace Phast\WebControls;
	
	use Phast\HTMLControl;
	use Phast\WebControl;
	use Phast\WebControlAttribute;

	use Phast\HTMLControls\Literal;
			
	class Blockquote extends WebControl
	{
		public function __construct()
		{
			parent::__construct();
			$this->TagName = "blockquote";
		}
		
		public $Author;
		public $Source;
		
		protected function RenderBeginTag()
		{
			$p = new HTMLControl("p");
			$p->Controls = $this->Controls;
			$this->Controls = array($p);
			
			if ($this->Author != null || $this->Source != null)
			{
				$small = new HTMLControl("small");
				
				if ($this->Author != null)
				{
					$litAuthor = new Literal();
					$litAuthor->Value = $this->Author . " ";
					$small->Controls[] = $litAuthor;
				}
				if ($this->Source != null)
				{
					$cite = new HTMLControl("cite");
					$cite->Attributes[] = new WebControlAttribute("title", $this->Source);
					$cite->InnerHTML = $this->Source;
					$small->Controls[] = $cite;
				}
				$this->Controls[] = $small;
			}
			
			parent::RenderBeginTag();
		}
	}
?>