<?php
    require_once 'SPArticle.class.php';
    require_once 'SPArticleShort.class.php';

    class SPDatabase {
        private $db;

        function __construct()
        {
            $this->db = new mysqli('p500271.mysql.ihc.ru','p500271_spok','qKKN36Eakw','p500271_spok');
            $this->db->query('SET CHARSET utf8');
        }

        function __destruct()
        {
            $this->db->close();
        }

        /**
         * @param string $category
         * @param int $subid
         * @return SPArticle|null
         */
        function getArticle($category,$subid) {
            $article = null;
            $category = $this->db->real_escape_string($category);
            if (!is_numeric($subid)) return null;
            if ($result = $this->db->query(sprintf('SELECT * FROM articles WHERE (category = \'%s\') AND (subid = %d)',$category,$subid))) {
                if ($data = $result->fetch_assoc()) $article = new SPArticle($data);
                $result->close();
            }
            return $article;
        }

        /**
         * @param string $category
         * @return SPArticleShort[]
         */
        function getArticles($category) {
            $articles = array();
            $category = $this->db->real_escape_string($category);
            if ($result = $this->db->query(sprintf('SELECT category, subid, title, date, cover FROM articles WHERE (visible > 0) AND (category = \'%s\') ORDER BY date DESC',$category))) {
                while ($data = $result->fetch_assoc()) $articles[] = new SPArticleShort($data);
                $result->close();
            }
            return $articles;
        }

        /**
         * @return SPArticleShort[]
         */
        function getAllArticles() {
            $articles = array();
            if ($result = $this->db->query('SELECT category, subid, title, date, cover, SUBSTRING(content,1,250) AS preview FROM articles WHERE visible > 0 ORDER BY date DESC')) {
                while ($data = $result->fetch_assoc()) $articles[] = new SPArticleShort($data);
                $result->close();
            }
            return $articles;
        }

        /**
         * @param SPArticle $article
         * @return object
         */
        function getPreviousAndNextArticle($article) {
            $articles = array();
            if ($result = $this->db->query(sprintf('SELECT id FROM articles WHERE (visible > 0) AND (category = \'%s\') ORDER BY date DESC',$article->category))) {
                while ($data = $result->fetch_assoc()) $articles[] = (int)$data['id'];
                $result->close();
            }
            $high = count($articles) - 1;
            if ($high==0) {
                $prev = $articles[0];
                $next = $prev;
            } else {
                $index = array_search($article->id,$articles);
                if ($high==1) {
                    $prev = $index==0 ? 1 : 0;
                    $next = $prev;
                } else {
                    $prev = $index - 1; $next = $index + 1;
                    if ($prev<0) $prev = $high;
                    if ($next>$high) $next = 0;
                }
            }
            $prev = $articles[$prev];
            $next = $articles[$next];
            $obj = new stdClass();
            $single = $high<2;
            if ($result = $this->db->query(sprintf('SELECT id, category, subid, title, date, cover FROM articles WHERE id IN (%d,%d)',$prev,$next))) {
                for ($i=0; $i<($single ? 1 : 2); $i++) {
                    $data = $result->fetch_assoc();
                    if ($single) {
                        $obj->prev = new SPArticleShort($data);
                        $obj->next = $obj->prev;
                    } else {
                        if ($data['id']==$next) $obj->next = new SPArticleShort($data); else $obj->prev = new SPArticleShort($data);
                    }
                }
                $result->close();
            }
            return $obj;
        }
    }
