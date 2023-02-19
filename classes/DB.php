<?php
ini_set('mysql.connect.timeout', 300);
ini_set('default_socket.timeout', 300);
class DB {

        private static function connect() {
                $pdo = new PDO('mysql:host=sql207.epizy.com;dbname=epiz_30647282_SocialNetwork;charset=utf8', 'epiz_30647282', 'lq6JyYlNtMdX0kI');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $pdo;
        }

        public static function query($query, $params = array()) {
                $statement = self::connect()->prepare($query);
                $statement->execute($params);

                if (explode(' ', $query)[0] == 'SELECT') {
                $data = $statement->fetchAll();
                return $data;
                }
        }

}
