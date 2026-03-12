<?php

/**
 * Class SPArticle
 * @property int $id
 * @property string $category
 * @property int $subid
 * @property string $date
 * @property string $title
 * @property string[] $images
 * @property string $place
 * @property string $content
 */
    class SPArticle {
        public $id;
        public $category;
        public $subid;
        public $date;
        public $title;
        public $images;
        public $place;
        public $content;

        function __construct($data)
        {
            $this->id = (int)$data['id'];
            $this->category = trim($data['category']);
            $this->subid = (int)$data['subid'];
            $this->date = trim($data['date']);
            $this->title = trim($data['title']);
            $this->images = explode(',',$data['images']);
            $this->place = trim($data['place']);
            $this->content = trim($data['content']);
        }

        /**
         * @return string
         */
        public function getCategory() {
            switch ($this->category) {
                case 'church': return 'Восстановление и строительство храмов';
                case 'publishing': return 'Издательская деятельность';
                case 'orphans': return 'Помощь детям-сиротам и больным детям';
                case 'veterans': return 'Помощь ветеранам войн';
                case 'art': return 'Творческая мастерская';
                default: return '';
            }
        }

        public function getCategoryUrl() {
            return '/' . $this->category;
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
    }