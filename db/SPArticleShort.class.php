<?php

/**
 * Class SPArticleShort
 * @property string $title
 * @property string $date
 * @property string $cover
 * @property string $category
 * @property int $subid
 * @property string $preview
 */
    class SPArticleShort {
        public $title;
        public $date;
        public $cover;
        public $category;
        public $subid;
        public $preview;

        function __construct($data)
        {
            $this->title = trim($data['title']);
            $this->date = trim($data['date']);
            $this->cover = trim($data['cover']);
            $this->category = trim($data['category']);
            $this->subid = (int)$data['subid'];
            if (array_key_exists('preview',$data)) $this->makePreviewText($data['preview']); else unset($preview);
        }

        /**
         * @param string $text
         */
        private function makePreviewText($text) {
            $this->preview = trim(mb_substr(strip_tags($text),0,200)) . '&hellip;';
        }

        /**
         * @return string
         */
        public function getDate() {
            $values = array_map(function($value) {
                return $value * 1;
            },explode('.',$this->date));
            $year = $values[0];
            $month = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь')[$values[1]-1];
            return $month . ' ' . $year;
        }

        public function getUrl() {
            return implode('/',array('','article',$this->category,$this->subid));
        }
    }