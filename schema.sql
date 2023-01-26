drop table if exists membership_levels;
drop table if exists admin_permissions;
drop table if exists members;


create table membership_levels (
    membership_levels_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    level_description varchar(50) NOT NULL,
    level_price_month decimal(10,2),
    level_price_year decimal(10,2)
);

create table members (
     member_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
     first_name varchar(30) NOT NULL,
     last_name varchar(30) NOT NULL,
     login_name varchar(30) NOT NULL,
     login_password varchar(30) NOT NULL,
     join_date DATE NOT NULL,
     email varchar(75),
     phone varchar(20),
     balance decimal(10,2) DEFAULT 0,
     visits int DEFAULT 0,
     membership_level int NOT NULL,   /* separate table */
        CONSTRAINT fk_m_membership_level FOREIGN KEY (membership_level) REFERENCES membership_levels(membership_levels_id)
);

create table admin_permissions (
    admin_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    member_id int NOT NULL,
        CONSTRAINT fk_ap_member_id FOREIGN KEY (member_id) REFERENCES members(member_id),
    view_member_details BOOLEAN,
    edit_member_details BOOLEAN,
    view_gym_memberships BOOLEAN,
    add_member BOOLEAN,
    suspend_member BOOLEAN
);
