<?php

namespace JsonRevisions\Model\Entity\Traits;

trait RevisableTrait
{
    /***
     * @return int|void
     * return a count of current reversions for the entity
     */
    protected function _getNumberOfReversions()
    {
        return count($this->revisions);
    }

    /***
     * @return mixed
     * virtual field that returns the first reversion on the stack
     * first reversion is oldest by nature
     */
    protected function _getFirstReversion()
    {
        return reset($this->revisions);
    }

    /***
     * @return mixed
     * virtual field to return the previously saved reversion on the stack
     */
    protected function _getPreviousReversion()
    {
        return array_slice($this->revisions, (($this->number_of_reversions) - 2), "1")[0];
    }

    /***
     * @param $key
     * @return $this|bool
     *  re-marshal the current entity with data from a previous reversion by passing the key.
     */
    public function restoreReversionByKey($key)
    {
        if (!in_array($key, array_keys($this->revisions))) {
            return false;
        }
        $oldData = $this->revisions[$key];
        foreach ($oldData as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    /***
     * @param $reversion
     * @return $this|bool
     *  re-marshal current entity with data from the specified reversion.
     */
    public function restoreReversion($reversion)
    {
        if (empty($reversion)) {
            return false;
        }
        foreach ($reversion as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    /***
     * @return RevisableTrait|bool
     *  re-marshal the current entity with data from the oldest (first) reversion
     */
    public function restoreFirstReversion()
    {
        return $this->restoreReversion($this->first_reversion);
    }

    /***
     * @return RevisableTrait|bool
     */
    public function restorePreviousreversion()
    {
        return $this->restoreReversion($this->previous_reversion);

    }

    /***
     * clear out the revisions array.
     */
    public function deleteAllReversions()
    {
        $this->set('revisions', []);
    }
}