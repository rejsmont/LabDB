<?php

/*
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace VIB\FliesBundle\Utils;

/**
 * Description of Genetics
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Genetics {
    
    /**
     * Cross two genotypes
     * 
     * @param string  $virgin Virgin genotype
     * @param string  $male   Male genotype
     * @param boolean $isMale Predict male offspring genotypes
     * @return array
     */
    public static function cross($virgin, $male, $isMale = false)
    {
        $virginChromosomes = static::getChromosomes($virgin);
        $maleChromosomes = static::getChromosomes($male);
        $virginMax = (count($virginChromosomes) > 0) ? max(array_keys($virginChromosomes)) : 0;
        $maleMax = (count($maleChromosomes) > 0) ? max(array_keys($maleChromosomes)) : 0;
                 
        $count = max($maleMax, $virginMax);
        $outcomes = array();
        $genotypes = array();
        
        for ($i = 0; $i <= $count; $i++) {
            if (($isMale)&&($i == 0)) {
                $maleChromosomes[$i] = isset($maleChromosomes[$i]) ? $maleChromosomes[$i] : array('+');
            } else {
                $maleChromosomes[$i] = isset($maleChromosomes[$i]) ? $maleChromosomes[$i] : array('+');
            }
            $virginChromosomes[$i] = isset($virginChromosomes[$i]) ? $virginChromosomes[$i] : array('+');

            foreach ($virginChromosomes[$i] as $virginAllele) {
                foreach ($maleChromosomes[$i] as $maleAllele) {
                    $maleAllele = trim($maleAllele);
                    $virginAllele = trim($virginAllele);
                    if (($maleAllele != '+')||($virginAllele != '+')) {
                        $virginFirst = ($virginAllele == $maleAllele) ? 
                                $virginAllele : $virginAllele . ' / ' . $maleAllele;
                        $maleFirst = ($virginAllele == $maleAllele) ? 
                                $maleAllele : $maleAllele . ' / ' . $virginAllele;
                        
                        if (static::allelePriority($virginAllele) > static::allelePriority($maleAllele)) {
                            $outcome = $maleFirst;
                        } else {
                            $outcome = $virginFirst;
                        }
                        
                        if ((!isset($outcomes[$i]))||
                                ((!in_array($virginFirst, $outcomes[$i]))&&
                                    (!in_array($maleFirst, $outcomes[$i])))) {
                            $outcomes[$i][] = $outcome;
                        }
                    }
                }
            }
        }
        
        if (count($outcomes > 0)) {
            foreach (static::cartesian($outcomes) as $genotype) {
                $genotypes[] = implode(" ; ", $genotype);
            }
            sort($genotypes);
        }
        
        return $genotypes;
    }
    
    /**
     * Extract chromosomes from genotype string
     * 
     * @param string $genotype
     * @return array
     */
    public static function getChromosomes($genotype)
    {
        $chromosomes = array();
        $chrs = explode("; ", $genotype);
        
        $offset = 0;
        foreach ($chrs as $key => $chromosome) {
            $guess = static::guessChromosome($chromosome);
            if (false !== $guess) {
                $offset = $guess - $key;
            }
            if (! empty($chromosome)) {
                $chromosomes[$key + $offset] = explode(" / ", $chromosome);
            }
        }
        
        return $chromosomes;
    }
    
    /**
     * Guess chromosome from genotype based on balancers
     * 
     * @param string $chromosome
     * @return int|boolean
     */
    public static function guessChromosome($chromosome)
    {
        if ((preg_match('/^F[Mm]\d/', $chromosome))||(preg_match('/\/ F[Mm]\d/', $chromosome))) {
            return 0;
        } elseif ((preg_match('/^S[Mm]\d/', $chromosome))||(preg_match('/\/ S[Mm]\d/', $chromosome))) {
            return 1;
        } elseif ((preg_match('/^CyO/', $chromosome))||(preg_match('/\/ CyO/', $chromosome))) {
            return 1;
        } elseif ((preg_match('/^T[Mm]\d/', $chromosome))||(preg_match('/\/ T[Mm]\d/', $chromosome))) {
            return 2;
        } elseif ((preg_match('/^MK?RS/', $chromosome))||(preg_match('/\/ MK?RS/', $chromosome))) {
            return 2;
        } else {
            return false;
        }
    }
    
    /**
     * Calculate allele priority
     * 
     * @param string $allele
     * @return int
     */
    public static function allelePriority($allele)
    {
        if ($allele == '+') {
            return 1;
        } elseif (preg_match('/^F[Mm]\d/', $allele)) {
            return 2;
        } elseif (preg_match('/^S[Mm]\d/', $allele)) {
            return 2;
        } elseif (preg_match('/^T[Mm]\d/', $allele)) {
            return 2;
        } elseif (preg_match('/^CyO/', $allele)) {
            return 2;
        } elseif (preg_match('/^MK?RS/', $allele)) {
            return 2;
        }
        
        return 0;
    }
    
    /**
     * N-dimensional cartesian product
     * 
     * @link http://stackoverflow.com/a/6313346 The original code
     * @author Jon [http://stackoverflow.com/users/50079/jon]
     * 
     * @param array $input
     * @return array
     */
    public static function cartesian($input)
    {
        $result = array();
        while (list($key, $values) = each($input)) {
            if (empty($values)) {
                continue;
            }
            if (empty($result)) {
                foreach($values as $value) {
                    $result[] = array($key => $value);
                }
            }
            else {
                $append = array();
                foreach($result as &$product) {
                    $product[$key] = array_shift($values);
                    $copy = $product;
                    foreach($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }
                    array_unshift($values, $product[$key]);
                }
                $result = array_merge($result, $append);
            }
        }

        return $result;
    }
}
