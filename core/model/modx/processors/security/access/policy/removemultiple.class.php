<?php
/*
 * This file is part of MODX Revolution.
 *
 * Copyright (c) MODX, LLC. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */

/**
 * Removes multiple policies
 *
 * @param integer $policies A comma-separated list of policies
 *
 * @package modx
 * @subpackage processors.security.access.policy
 */
class modAccessPolicyRemoveMultipleProcessor extends modObjectProcessor {
    public $languageTopics = array('policy');
    public $permission = 'policy_delete';
    public $objectType = 'policy';

    public function process() {
        $policies = $this->getProperty('policies');
        if (empty($policies)) {
            return $this->failure($this->modx->lexicon('policy_err_ns'));
        }

        $policyIds = is_array($policies) ? $policies : explode(',', $policies);
        $core = array('Resource','Object','Administrator','Element','Load Only','Load, List and View');

        foreach ($policyIds as $policyId) {
            /** @var modAccessPolicy $policy */
            $policy = $this->modx->getObject('modAccessPolicy', $policyId);
            if (empty($policy)) {
                continue;
            }

            if (in_array($policy->get('name'), $core)) {
                continue;
            }

            if (!$policy->remove()) {
                $this->modx->log(
                    modX::LOG_LEVEL_ERROR,
                    $this->modx->lexicon('policy_err_remove') . print_r($policy->toArray(), true)
                );
            }
            $this->logManagerAction($policy);
        }

        return $this->success();
    }

    public function logManagerAction(modAccessPolicy $policy) {
        $this->modx->logManagerAction('remove_policy', 'modAccessPolicy', $policy->get('id'));
    }
}
return 'modAccessPolicyRemoveMultipleProcessor';
