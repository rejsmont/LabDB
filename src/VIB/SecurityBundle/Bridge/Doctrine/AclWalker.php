<?php

namespace VIB\SecurityBundle\Bridge\Doctrine;

use Doctrine\ORM\Query\SqlWalker;


/**
 * Description of ACLWalker
 *
 * @link https://gist.github.com/mailaneel/1363377 Original code on gist
 * 
 * @author mailaneel
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclWalker extends SqlWalker
{

//    /**
//     * {@inheritdoc}
//     */
//    public function walkSelectClause($selectClause)
//    {
//        $sql = parent::walkSelectClause($selectClause);
//
//        if (! $selectClause->isDistinct) {
//            $sql = str_replace('SELECT', 'SELECT DISTINCT', $sql);
//        }
//
//        return $sql;
//    }
    
    /**
     * {@inheritdoc}
     */
    public function walkFromClause($fromClause)
    {
        $sql = parent::walkFromClause($fromClause);
        $tableAlias = $this->getSQLTableAlias($this->getQuery()->getHint('acl.entityRootTableName'),
                $this->getQuery()->getHint('acl.entityRootTableDqlAlias'));
        $extraQuery = $this->getQuery()->getHint('acl.extra.query');


        $tempAclView = <<<tempAclView
        JOIN ({$extraQuery}) ta_ ON {$tableAlias}.id = ta_.id
tempAclView;

        return $sql . $tempAclView;
    }

}
