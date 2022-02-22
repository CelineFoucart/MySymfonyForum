<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

class PermissionHelper
{
    const PUBLIC_ACCESS = 'PUBLIC_ACCESS';

    public function setPermissions(QueryBuilder $builder, array $permissions = [])
    {
        if(empty($permissions)) {
            $permissions = [self::PUBLIC_ACCESS];
        }

        $orStatements = $builder->expr()->orX();
        $i = 0;
        foreach ($permissions as $permission) {
            $i++;
            $orStatements->add(
                $builder->expr()->like('c.permissions', $builder->expr()->literal('%' . $permission . '%'))
            );
        }
        $builder->andWhere($orStatements);
        return $builder;
    }
}