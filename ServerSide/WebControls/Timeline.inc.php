<?php
	namespace Phast\WebControls;
	
	use Phast\WebControl;
	use Phast\HTMLControl;
	
	class Timeline extends WebControl
	{
		public $Posts;
		
		public function __construct()
		{
			$this->ParseChildElements = false;
			$this->ClassList[] = "Timeline";
			$this->TagName = "div";
		}
		
		protected function RenderBeginTag()
		{
			$this->Controls = array();
			foreach ($this->Posts as $post)
			{
				$divPost = new HTMLControl("div");
				$divPost->ClassList[] = "TimelinePost";
				
				$divPostHeader = new HTMLControl("div");
				$divPostHeader->ClassList[] = "Header";
				$divPost->Controls[] = $divPostHeader;
				
				$divPostContent = new HTMLControl("div");
				$divPostContent->ClassList[] = "Content";
				$divPost->Controls[] = $divPostContent;
				
				$divPostFooter = new HTMLControl("div");
				$divPostFooter->ClassList[] = "Footer";
				$divPost->Controls[] = $divPostFooter;
				
				$this->Controls[] = $divPost;
			}
			parent::RenderBeginTag();
		}
	}
	class TimelinePost
	{
		public $ID;
		public $CreationUserName;
		public $CreationUserImageURL;
		public $Controls;
		public $ViewCount;
		
		public function __construct()
		{
			$this->ParseChildElements = true;
		}
	}
?>