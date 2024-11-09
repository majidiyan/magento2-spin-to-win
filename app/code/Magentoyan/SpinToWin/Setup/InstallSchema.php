<?php

namespace Magentoyan\SpinToWin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface {

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'spin_to_win_customer'
         */
        $table = $installer->getConnection()
                ->newTable($installer->getTable('spin_to_win_customer'))
                ->addColumn(
                        'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'PK'
                )
                
                ->addColumn(
                        'customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true], 'Customer Id'
                )
                ->addColumn(
                        'coupon_code', 
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255, ['nullable' => true, 'default' => null], 'Coupon Code'
                )
                
                ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        ['nullable' => true, 'default' => null],
                        'Status'
                )
                
                ->addColumn(
                        'reaction', 
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32, ['nullable' => true, 'default' => null], 'Reaction'
                )
                
                ->addColumn(
                        'free_shipping',
                        \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        null,
                        ['nullable' => false, 'default' => 0],
                        'Free Shipping'
                )
                
                
                ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            null,
                            [],
                        'The date when the record was created'
                )
                ->addColumn(
                        'updated_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                            null,
                            [],
                        'The date when the record was updated'
                )
                ->addIndex(
                        $installer->getIdxName('spin_to_win_customer', ['coupon_code']), ['coupon_code']
                )
                ->addIndex(
                        $installer->getIdxName('spin_to_win_customer', ['customer_id']), ['customer_id']
                )
                ->addIndex(
                        $installer->getIdxName('spin_to_win_customer', ['reaction']), ['reaction']
                )
                ->addForeignKey(
                        $installer->getFkName('spin_to_win_customer', 'customer_id', 'customer_entity', 'entity_id'), 'customer_id', $installer->getTable('customer_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
               
        ;
        $installer->getConnection()->createTable($table);
        
        
        
        
        //-- second
        
        $table = $installer->getConnection()
                ->newTable($installer->getTable('spin_to_win_slices'))
                ->addColumn(
                        'entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'PK'
                )
                
                
                ->addColumn(
                        'reaction', 
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32, ['nullable' => true, 'default' => null], 'Reaction'
                )
                
                ->addColumn(
                        'label', 
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255, ['nullable' => true, 'default' => null], 'Label'
                )
                
                ->addColumn(
                        'background_color',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        ['nullable' => true, 'default' => null],
                        'Background Color'
                )
                
                ->addColumn(
                        'text_color',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        ['nullable' => true, 'default' => null],
                        'Text Color'
                )
                ->addColumn(
                        'chance',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Chance'
                )
                
                ->addColumn(
                        'free_shipping',
                        \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        null,
                        ['nullable' => false, 'default' => 0],
                        'Free Shipping'
                )
                
                ->addColumn(
                        'pooch',
                        \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                        null,
                        ['nullable' => false, 'default' => 0],
                        'Pooch'
                )
                
                ->addColumn(
                        'coupon_type_action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        32,
                        ['nullable' => false, 'default' => 'percent'],
                        'Coupon Type Action'
                )
                
                ->addColumn(
                        'description',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '4k',
                        ['nullable' => true, 'default' => null],
                        'Description'
                )
                
                ->addColumn(
                        'conditions_serialized',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        '4k',
                        ['nullable' => true, 'default' => null],
                        'Conditions Serialized'
                )
                
                ->addColumn(
                        'coupon_value_action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Coupon Value Action'
                )
                
                ->addColumn(
                        'coupon_lifetime',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                        'Coupon Life Time'
                )
                ->addIndex(
                        $installer->getIdxName('spin_to_win_slices', ['reaction']), ['reaction']
                )
                
                
                ;
        $installer->getConnection()->createTable($table);
        
        //-- second end

        $installer->endSetup();
    }
}
