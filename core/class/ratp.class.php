<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class ratp extends eqLogic {
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom*/
    public static function cron() {
        if ($_eqLogic_id == null) { // La fonction n’a pas d’argument donc on recherche tous les équipements du plugin
            $eqLogics = self::byType('ratp', true);
        } else {// La fonction a l’argument id(unique) d’un équipement(eqLogic)
            $eqLogics = array(self::byId($_eqLogic_id));
        }
        
        foreach ($eqLogics as $ratp) {
            if ($ratp->getIsEnable() == 1) {//vérifie que l'équipement est acitf
                $cmd = $ratp->getCmd(null, 'refresh');//retourne la commande "refresh si elle exxiste
                if (!is_object($cmd)) {//Si la commande n'existe pas
                    continue; //continue la boucle
                }
                $cmd->execCmd(); // la commande existe on la lance
            }
        }
    }


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



    /*     * *********************Méthodes d'instance************************* */

    public function preInsert() {
        
    }

    public function postInsert() {
        
    }

    public function preSave() {
        $this->setDisplay("showNameOndashboard",0);
    }

    public function postSave() {
        if($this->getConfiguration("urlapi") == ""){
            $this->setConfiguration("urlapi","automatique");
        }
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new ratpCmd();
            $refresh->setName(__('Rafraichir', __FILE__));
        }
        $refresh->setEqLogic_id($this->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();
        
        $ligne = $this->getCmd(null, 'ligne');
        if (!is_object($ligne)) {
            $ligne = new ratpCmd();
            $ligne->setName(__('Ligne:', __FILE__));
        }
        $ligne->setLogicalId('ligne');
        $ligne->setEqLogic_id($this->getId());
        $ligne->setType('info');
        $ligne->setSubType('string');
        $ligne->save();
        
        $stop = $this->getCmd(null, 'stop');
        if (!is_object($stop)) {
            $stop = new ratpCmd();
            $stop->setName(__('Arret:', __FILE__));
        }
        $stop->setLogicalId('stop');
        $stop->setEqLogic_id($this->getId());
        $stop->setType('info');
        $stop->setSubType('string');
        $stop->save();
        
        $direction = $this->getCmd(null, 'direction');
        if (!is_object($direction)) {
            $direction = new ratpCmd();
            $direction->setName(__('Direction:', __FILE__));
        }
        $direction->setLogicalId('direction');
        $direction->setEqLogic_id($this->getId());
        $direction->setType('info');
        $direction->setSubType('string');
        $direction->save();
        
        $passage1 = $this->getCmd(null, 'passage1');
        if (!is_object($passage1)) {
            $passage1 = new ratpCmd();
            $passage1->setName(__('Passage 1:', __FILE__));
        }
        $passage1->setLogicalId('passage1');
        $passage1->setEqLogic_id($this->getId());
        $passage1->setType('info');
        $passage1->setSubType('string');
        $passage1->save();
        
        $passage2 = $this->getCmd(null, 'passage2');
        if (!is_object($passage2)) {
            $passage2 = new ratpCmd();
            $passage2->setName(__('Passage 2:', __FILE__));
        }
        $passage2->setLogicalId('passage2');
        $passage2->setEqLogic_id($this->getId());
        $passage2->setType('info');
        $passage2->setSubType('string');
        $passage2->save();
        
        
    }

    public function preUpdate() {
        
    }

    public function postUpdate() {
        
    }

    public function preRemove() {
        
    }

    public function postRemove() {
        
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class ratpCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    public function execute($_options = array()) {
        $eqlogic = $this->getEqLogic();
        switch ($this->getLogicalId()) {
            case 'refresh':
                $url = "https://api-ratp.pierre-grimaud.fr/v4/schedules/"; //buses/121/Fort%20de%20Rosny/R
                $url = $url.$eqlogic->getConfiguration("urlapi");
                $json = file_get_contents($url);
                if(!$json){
                    $eqlogic->checkAndUpdateCmd('ligne', "Erreur API!");
                    $eqlogic->checkAndUpdateCmd('passage1', "");
                    $eqlogic->checkAndUpdateCmd('passage2', "");
                    $eqlogic->checkAndUpdateCmd('direction', "");
                    $eqlogic->checkAndUpdateCmd('stop', $url);
                    break;
                }
                $data = json_decode($json,true);
                $passage1 = $data[result]["schedules"][0]["message"];
                $passage2 = $data[result]["schedules"][1]["message"];
                $dest = $data[result]["schedules"][0]["destination"];
                $tab = explode ( "/" , $url);
                
                switch ($tab[5]) {
                    case 'buses':
                        $type = "Bus";
                    break;
                    case 'metros':
                        $type = "Metro";
                    break;
                    case 'rers':
                        $type = "Rer";
                    break;
                    case 'tramways':
                        $type = "Tramway";
                    break;
                    case 'noctiliens':
                        $type = "Noctilien";
                    break;
                }
                
                $line = $type." ".$tab[6];
                $stop = str_replace("%20"," ",$tab[7]);
                
                
                
                $eqlogic->checkAndUpdateCmd('passage1', $passage1);
                $eqlogic->checkAndUpdateCmd('passage2', $passage2);
                $eqlogic->checkAndUpdateCmd('direction', $dest);
                $eqlogic->checkAndUpdateCmd('stop', $stop);
                $eqlogic->checkAndUpdateCmd('ligne', $line);
                
                break;
        }
    }

    /*     * **********************Getteur Setteur*************************** */
}


