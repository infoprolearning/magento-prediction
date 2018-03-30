<?php
    class Compunnel_Prediction_Helper_Data extends Compunnel_Prediction_Helper_Abstract
    {
        public function makeRecommendationCall($data)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://52.55.22.136:8000/queries.json");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_POST, 1);
            $headers = array();
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

        protected function getRecommendationUrl()
        {
        }
    }
