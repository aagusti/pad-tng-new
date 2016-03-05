<?
 class dt_users extends CI_Model implements DatatableModel{ //


        public function appendToSelectStr() {
        /*
                return array(
                    'city_state_zip' => 'concat(s.s_name, \'  \', c.c_name, \'  \', c.c_zip)'
                );
        */
          return NULL;
        }
        public function fromTableStr() {
            return 'app.users u';
        }



        public function joinArray(){
          return null;
        }
        /*
            return array(
              'city c|left outer' => 'c.state_id = s.id',
              'user u' => 'u.state_id = s.id'
              );
        */
        public function whereClauseArray(){
          return null;
        }
        /*
            return array(
                'u.id' => $this -> ion_auth -> get_user_id() 
                );
        */
   }
   ?>