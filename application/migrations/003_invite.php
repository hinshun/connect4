<?php
class Migration_Invite extends CI_Migration {

  public function up()
  {
    $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
    $this->dbforge->add_field("user1_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("user2_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("invite_status_id int(11) unsigned NOT NULL");

    $this->dbforge->add_key('id', TRUE);

    $this->dbforge->create_table('invite', TRUE);
  }

  public function down()
  {
    $this->dbforge->drop_table('invite');
  }

}

