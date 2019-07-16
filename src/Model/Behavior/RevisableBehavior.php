<?php

namespace JsonRevisions\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;

/**
 * Revisable behavior
 */
class RevisableBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     *  set numberOfRevisionsToKeep to 0 to save unlimited reversions.
     */
    protected $_defaultConfig = [
        'omittedFields' => ['id', 'revisions', 'created', 'modified'],
        'numberOfRevisionsToKeep' => 0,
    ];

    public function initialize(array $config)
    {
        // Some initialization code here
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if ($entity->isDirty()) {
            $this->setRevision($entity);
        }
    }

    private function setRevision(Entity $entity)
    {
        if(!is_null($entity->revisions) && !empty($entity->revisions)){
            $currentRevisions = $this->removeOldRevisions($entity);
        }
        $newRevision = $this->prepareNewRevision($entity);
        $currentRevisions[date("YmdHis")] = $newRevision;
        return $entity->set('revisions', $currentRevisions);
    }

    /***
     * @return array
     * prepares the the entity data to be saved as a revision
     * The revision data that will be returned is in the format of
     * ['20190120112352'=>[...]]
     */
    private function prepareNewRevision(Entity $entity)
    {
        $config = $this->getConfig();
        $newRevisionData = $entity->toArray();
        foreach ($config['omittedFields'] as $field) {
            unset($newRevisionData[$field]);
        }
        return $newRevisionData;
    }

    /***
     * @return mixed
     * remove older reversions when the number of reversions
     * match or excede that of of numberOfrevisionsToKeep.
     */
    private function removeOldRevisions(Entity $entity)
    {
        $currenEntity = $entity->toArray();
        $currentRevisions = $currenEntity["revisions"];
        if (empty($currenEntity["revisions"])) {
            return $currentRevisions;
        }

        if ((count($currentRevisions) >= $this->getConfig('numberOfRevisionsToKeep')) && $this->getConfig('numberOfRevisionsToKeep') > 0) {
            $remove = true;
            while ($remove) {
                if ((count($currentRevisions) < $this->getConfig('numberOfRevisionsToKeep')) && $this->getConfig('numberOfRevisionsToKeep') > 1) {
                    $remove = false;
                    break;
                }
                $keys = array_keys($currentRevisions);
                ksort($keys);
                unset($currentRevisions[reset($keys)]);
                unset($keys[reset($keys)]);
            }
        }
        return $currentRevisions;
    }
}