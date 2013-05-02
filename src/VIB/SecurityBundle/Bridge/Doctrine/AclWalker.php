<?php

/*
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 *
 * Original code by mailaneel is available at
 * https://gist.github.com/mailaneel/1363377
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

namespace VIB\SecurityBundle\Bridge\Doctrine;

use Doctrine\ORM\Query\SqlWalker;

/**
 * The AclWalker is a TreeWalker that walks over a DQL AST and constructs
 * the corresponding SQL.
 *
 * @link https://gist.github.com/mailaneel/1363377 Original code on gist
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * @author mailaneel
 */
class AclWalker extends SqlWalker
{
    /**
     * {@inheritdoc}
     */
    public function walkFromClause($fromClause)
    {
        $sql = parent::walkFromClause($fromClause);
        $aclMetadata = $this->getQuery()->getHint('acl.metadata');
        
        if ($aclMetadata) {
            foreach ($aclMetadata as $key => $metadata) {
                $alias = $metadata['alias'];
                $query = $metadata['query'];
                $table = $metadata['table'];
                $tableAlias = $this->getSQLTableAlias($table, $alias);
                $aclAlias = 'ta' . $key . '_';
                                
                $aclSql = <<<ACL_SQL
INNER JOIN ({$query}) {$aclAlias} ON {$tableAlias}.id = {$aclAlias}.id
ACL_SQL;
                $sql .= ' ' . $aclSql;
            }
        }
        
        return $sql;
    }

}
