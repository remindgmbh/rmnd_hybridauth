# Add new field to fe_users
CREATE TABLE fe_users (
    # 1:n connection
    tx_rmndhybridauth_domain_model_connections int(11) unsigned DEFAULT '0' NOT NULL

);

# Connection for fe_users to provider
CREATE TABLE tx_rmndhybridauth_domain_model_connection (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

    # fe_user for this connection
    fe_user int(11) unsigned DEFAULT '0' NOT NULL,
    # Defined provider in settings, not necessary the hybridauth provider name
    provider varchar(55) DEFAULT '' NOT NULL,
    # Identifier of user for this provider
    identifier varchar(255) DEFAULT '' NOT NULL,
    # Hash to identify for login
    login_hash varchar(255) DEFAULT '' NOT NULL,
    # Timestamp for last validation with provider, used for login validation
    last_validation int(11) unsigned DEFAULT '0' NOT NULL,

    tstamp int(11) unsigned DEFAULT '0' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,
    cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
    deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
    hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
	KEY parent (pid),
    KEY provider_identifier_idx (provider, identifier)

) ENGINE=InnoDB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

