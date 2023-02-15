/* version 1 */
drop table if exists membership_levels;
drop table if exists admin_permissions;
drop table if exists members;


create table membership_levels (
    membership_levels_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    level_name varchar(50) NOT NULL,
    level_price_month decimal(10,2),
    level_price_year decimal(10,2)
);

create table members (
     member_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
     first_name varchar(30) NOT NULL,
     last_name varchar(30) NOT NULL,
     login_name varchar(30) NOT NULL,
     login_password varchar(64) NOT NULL,
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

create table visits (
    visits_id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    member_id int NOT NULL,
        CONSTRAINT fk_m_members FOREIGN KEY (member_id) REFERENCES members(member_id),
    visit_date DATE NOT NULL
);

insert into membership_levels (level_name, level_price_month, level_price_year) values ('Bronze', 70.00, 700.00);
insert into membership_levels (level_name, level_price_month, level_price_year) values ('Silver', 90.00, 900.00);
insert into membership_levels (level_name, level_price_month, level_price_year) values ('Gold', 120.00, 1200.00);

/* sha1('password') == '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8' */
insert into members
    (first_name, last_name, login_name, login_password, join_date, email, phone, balance, visits, membership_level)
    values ('Jane', 'Doe', 'jdoe', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', '2022-12-01', 'jane@email.com', '206-555-1212', 248.00, 12, 1);


