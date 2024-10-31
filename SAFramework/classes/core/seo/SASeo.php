<?php
if(!class_exists('SASeo')){
	abstract class SASeo {
		var $subject;
		var $description;
		var $keywords;
		var $rating;
		var $author;
		var $author_date;
		var $copyright;
		var $og_title;
		var $og_content;
		var $link;
		var $classification;
		var $publisher;
		var $expires = 'never';
		var $distribution = 'global';
		
		public abstract function init();
		
		public function output() {
			$args = array (
					'description' => $this->description,
					'keywords' => $this->keywords,
					'rating' => $this->rating,
					'author' => $this->author,
					'author-date(date)' => $this->author_date,
					'copyright' => $this->copyright,
					'og_title' => $this->og_title,
					'og_content' => $this->og_content,
					'link' => $this->link,
					'publisher' => $this->publisher,
					'expires' => $this->expires,
					'distribution' => $this->distribution,
					'classification' => $this->classification 
			);
			
			echo '<!-- seo start-->' . PHP_EOL;
			
			foreach ( $args as $meta => $value ) {
				if (! empty ( $value )) {
					echo '<meta name="' . $meta . '" content="' . preg_replace ( '/\s/', ' ', trim ( strip_tags ( $value ) ) ) . '">' . PHP_EOL;
				}
			}
			
			echo '<!-- seo end-->' . PHP_EOL;
		}
		
		/**
		 *
		 * @return the $description
		 */
		public function getDescription() {
			return $this->description;
		}
		
		/**
		 *
		 * @return the $keywords
		 */
		public function getKeywords() {
			return $this->keywords;
		}
		
		/**
		 *
		 * @return the $rating
		 */
		public function getRating() {
			return $this->rating;
		}
		
		/**
		 *
		 * @return the $author
		 */
		public function getAuthor() {
			return $this->author;
		}
		
		/**
		 *
		 * @return the $copyright
		 */
		public function getCopyright() {
			return $this->copyright;
		}
		
		/**
		 *
		 * @return the $og_title
		 */
		public function getOg_title() {
			return $this->og_title;
		}
		
		/**
		 *
		 * @return the $og_content
		 */
		public function getOg_content() {
			return $this->og_content;
		}
		
		/**
		 *
		 * @return the $link
		 */
		public function getLink() {
			return $this->link;
		}
		
		/**
		 *
		 * @return the $expires
		 */
		public function getExpires() {
			return $this->expires;
		}
		
		/**
		 *
		 * @return the $distribution
		 */
		public function getDistribution() {
			return $this->distribution;
		}
		
		/**
		 *
		 * @param field_type $description        	
		 */
		public function setDescription($description) {
			$this->description = $description;
		}
		
		/**
		 *
		 * @param field_type $keywords        	
		 */
		public function setKeywords($keywords) {
			$this->keywords = $keywords;
		}
		
		/**
		 *
		 * @param field_type $rating        	
		 */
		public function setRating($rating) {
			$this->rating = $rating;
		}
		
		/**
		 *
		 * @param field_type $author        	
		 */
		public function setAuthor($author) {
			$this->author = $author;
		}
		
		/**
		 *
		 * @param field_type $copyright        	
		 */
		public function setCopyright($copyright) {
			$this->copyright = $copyright;
		}
		
		/**
		 *
		 * @param field_type $og_title        	
		 */
		public function setOg_title($og_title) {
			$this->og_title = $og_title;
		}
		
		/**
		 *
		 * @param field_type $og_content        	
		 */
		public function setOg_content($og_content) {
			$this->og_content = $og_content;
		}
		
		/**
		 *
		 * @param field_type $link        	
		 */
		public function setLink($link) {
			$this->link = $link;
		}
		
		/**
		 *
		 * @param string $expires        	
		 */
		public function setExpires($expires) {
			$this->expires = $expires;
		}
		
		/**
		 *
		 * @return the $subject
		 */
		public function getSubject() {
			return $this->subject;
		}
		
		/**
		 *
		 * @return the $classification
		 */
		public function getClassification() {
			return $this->classification;
		}
		
		/**
		 *
		 * @return the $publisher
		 */
		public function getPublisher() {
			return $this->publisher;
		}
		
		/**
		 *
		 * @param field_type $subject        	
		 */
		public function setSubject($subject) {
			$this->subject = $subject;
		}
		
		/**
		 *
		 * @param field_type $classification        	
		 */
		public function setClassification($classification) {
			$this->classification = $classification;
		}
		
		/**
		 *
		 * @param field_type $publisher        	
		 */
		public function setPublisher($publisher) {
			$this->publisher = $publisher;
		}
		
		/**
		 *
		 * @param string $distribution        	
		 */
		public function setDistribution($distribution) {
			$this->distribution = $distribution;
		}
		/**
		 *
		 * @return the $author_date
		 */
		public function getAuthor_date() {
			return $this->author_date;
		}
		
		/**
		 *
		 * @param field_type $author_date        	
		 */
		public function setAuthor_date($author_date) {
			$this->author_date = $author_date;
		}
	}
}