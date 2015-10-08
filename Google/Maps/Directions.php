<?php

    namespace Google\Maps;


    /**
     * Class Directions
     * @package Google\Maps
     *
     * @url https://developers.google.com/maps/documentation/directions/intro
     */
    class Directions {

        private $endpoint = "https://maps.googleapis.com/maps/api/directions/";
        private $format = 'json';
        private $options = array(
            'key'          => '',
            'destinations' => '',
            'origins'      => '',
            'language'     => 'fr'
        );

        public $error = null;
        public $response = null;



        /**
         * @param string $format
         * @param string $key
         * @param array  $options
         *
         * @throws \Exception
         */
        public function __construct($format = 'json', $key = '', $options = array()) {
            if(is_array($format)) {
                $this->setOptions($format);
            } else if($format != 'xml' && $format != 'json') {
                throw new \Exception("Le format de retour ne peut &ecirc;tre qu'en JSON ou XML");
            } else {
                $this->format = $format;
            }

            if(is_array($key)) {
                $this->setOptions($key);
            } else {
                $this->options['key'] = $key;
            }

            if(!is_array($options)) {
                throw new \Exception("Les options doivent &ecirc;tre un tableau");
            } else {
                $this->setOptions($options);
            }
        }



        /**
         * @param $options
         *
         * @throws \Exception
         */
        public function setOptions($options) {
            if(!is_array($options)) {
                throw new \Exception("Les options doivent &ecirc;tre un tableau");
            } else {
                if(array_key_exists('format', $options)) {
                    $this->format = $options['format'];
                    unset($options['format']);
                }

                $this->options = array_merge($options, $this->options);
            }
        }



        /**
         * @param string $language
         *
         * @url https://developers.google.com/maps/faq#languagesupport
         */
        public function setLanguage($language = 'fr') {
            $this->options['language'] = $language;
        }



        /**
         * @param $origin
         *
         * @throws \Exception
         */
        public function setOrigin($origin) {
            if($origin == null) {
                throw new \Exception("L'origine est vide");
            } else {
                $this->options['origin'] = $origin;
            }
        }



        /**
         * @param $destination
         *
         * @throws \Exception
         */
        public function setDestination($destination) {
            if($destination == null) {
                throw new \Exception("La destination est vide");
            } else {
                $this->options['destination'] = $destination;
            }
        }



        public function request($origin = '', $destination = '') {
            if($origin != '') {
                $this->setOrigin($origin);
            }

            if($destination != '') {
                $this->setDestination($destination);
            }

            if($this->options['key'] == '') {
                unset($this->options['key']);
            }


            $curl = curl_init();


            curl_setopt_array($curl, array(
                CURLOPT_URL            => $this->endpoint . $this->format . '?' . http_build_query($this->options),
                CURLOPT_POST           => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE        => 1
            ));

            $this->response = curl_exec($curl);

            if(!$this->response) {
                $this->error = "Erreur cURL:<br>Erreur n&deg;".curl_errno($curl)."<br><b>".curl_error($curl)."</b>";
                curl_close($curl);
                return false;
            } else {
                $this->response = json_decode($this->response);

                switch($this->response->status) {
                    case 'OK':
                        return true;
                        break;

                    case 'NOT_FOUND':
                        $this->error = "Erreur API:<br>Une des localisations n'a pu &eacute;tre trouvée.";
                        break;

                    case 'ZERO_RESULTS':
                        $this->error = "Erreur API:<br>Aucune route n'a pu &eacute;tre trouvée entre ces 2 points.";
                        break;

                    case 'MAX_WAYPOINTS_EXCEEDED':
                        $this->error = "Erreur API:<br>Il y a trop de points de passages (waypoints).";
                        break;

                    case 'INVALID_REQUEST':
                        $this->error = "Erreur API:<br>Requ&ecirc;te invalide. V&eacute;rifiez vos options.";
                        break;

                    case 'MAX_ELEMENTS_EXCEEDED':
                        $this->error = "Erreur API:<br>Le nombre de destination de depart ou de fin d&eacute;passe la limite.";
                        break;

                    case 'OVER_QUERY_LIMIT':
                        $this->error = "Erreur API:<br>Le nombre maximal de requ&ecirc;te pour cette application est d&eacute;pass&eacute;.";
                        break;

                    case 'REQUEST_DENIED':
                        $this->error = "Erreur API:<br>Le serveur de Google a refus&eacute; l'accès à l'API.";
                        break;

                    default:
                        $this->error = "Erreur API:<br>Une erreur inconnue a &eacute;t&eacute; rencontr&eacute; durant l'utilisation.";
                        break;
                }

                if(isset($this->response->error_message)) {
                    $this->error = $this->error . "<br>" . $this->response->error_message;
                }

                return false;
            }
        }


    }
