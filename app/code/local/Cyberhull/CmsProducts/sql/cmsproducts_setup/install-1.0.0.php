<?php
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$cmsPageFkName = $installer->getFkName('cmsproducts/cms_page_product', 'page_id', 'cms/page', 'page_id');
$productFkName = $installer->getFkName('cmsproducts/cms_page_product', 'product_id', 'catalog/product', 'entity_id');
$table = $installer->getConnection()
    ->newTable($installer->getTable('cmsproducts/cms_page_product'))
    ->addColumn('page_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Cms Page ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true
    ), 'Position')
    ->addIndex($installer->getIdxName('cmsproducts/cms_page_product', array('page_id', 'product_id', 'position')),
        array('page_id', 'product_id', 'position'))
    ->addForeignKey($cmsPageFkName, 'page_id', $installer->getTable('cms/page'), 'page_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($productFkName, 'product_id', $installer->getTable('catalog/product'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Cms Pages to Products Relations');
$installer->getConnection()->createTable($table);
$installer->endSetup();