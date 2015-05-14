<?php

use yii\db\Schema;
use yii\db\Migration;

class m150514_015846_create_user_profile_t extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_profile}}', [
            'id' => Schema::TYPE_PK,
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'first_name' => Schema::TYPE_STRING . ' NOT NULL',
            'last_name' => Schema::TYPE_STRING . ' NOT NULL',
            'has_chinese_name' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'chinese_name' => Schema::TYPE_STRING,
            'address' => Schema::TYPE_STRING . ' NOT NULL',
            'country' => Schema::TYPE_STRING . ' NOT NULL',
            'postcode' => Schema::TYPE_STRING . ' NOT NULL',
            'telephone' => Schema::TYPE_STRING . ' NOT NULL',
            'cellphone' => Schema::TYPE_STRING . ' NOT NULL',
            'age' => Schema::TYPE_INTEGER . ' NOT NULL',
            'gender' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'birthday' => Schema::TYPE_DATE . ' NOT NULL',
            'place_of_birth' => Schema::TYPE_STRING . ' NOT NULL',
            'nationality' => Schema::TYPE_STRING . ' NOT NULL',
            'race' => Schema::TYPE_STRING . ' NOT NULL',
            'has_dance_exp' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'dance_exp_from' => Schema::TYPE_STRING,
            'where_to_know' => Schema::TYPE_STRING . ' NOT NULL',
            'father_name' => Schema::TYPE_STRING,
            'father_identity_no' => Schema::TYPE_STRING,
            'father_email' => Schema::TYPE_STRING,
            'father_occupation' => Schema::TYPE_STRING,
            'father_cellphone' => Schema::TYPE_STRING,
            'father_office_no' => Schema::TYPE_STRING,
            'mother_name' => Schema::TYPE_STRING,
            'mother_identity_no' => Schema::TYPE_STRING,
            'mother_email' => Schema::TYPE_STRING,
            'mother_occupation' => Schema::TYPE_STRING,
            'mother_cellphone' => Schema::TYPE_STRING,
            'mother_office_no' => Schema::TYPE_STRING,

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        // echo "m150514_015846_create_user_profile_t cannot be reverted.\n";

        // return false;
        $this->dropTable('{{%user_profile}}');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
