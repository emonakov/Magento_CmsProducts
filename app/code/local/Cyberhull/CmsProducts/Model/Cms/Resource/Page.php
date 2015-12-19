<?php
class Cyberhull_CmsProducts_Model_Cms_Resource_Page extends Mage_Cms_Model_Resource_Page
{
    /**
     * Process page data before deleting
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'page_id = ?'     => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('cmsproducts/cms_page_product'), $condition);

        return parent::_beforeDelete($object);
    }

    public function getProductIds($pageId)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('cmsproducts/cms_page_product'), 'product_id')
            ->where('page_id = ?',(int)$pageId);

        return $adapter->fetchCol($select);
    }

    /**
     * Assign page to store views
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->hasProductId()) {
            $oldProducts = $this->getProductIds($object->getId());
            $newProducts = (array)$object->getProductId();
            $table = $this->getTable('cmsproducts/cms_page_product');
            $insert = array_diff($newProducts, $oldProducts);
            $delete = array_diff($oldProducts, $newProducts);

            if ($delete) {
                $where = array(
                    'page_id = ?'       => (int)$object->getId(),
                    'product_id IN (?)' => $delete
                );

                $this->_getWriteAdapter()->delete($table, $where);
            }

            if ($insert) {
                $data = array();

                foreach ($insert as $productId) {
                    $data[] = array(
                        'page_id'    => (int)$object->getId(),
                        'product_id' => (int)$productId
                    );
                }

                $this->_getWriteAdapter()->insertMultiple($table, $data);
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $products = $this->getProductIds($object->getId());
            $object->setData('product_id', $products);
        }

        return parent::_afterLoad($object);
    }
}
