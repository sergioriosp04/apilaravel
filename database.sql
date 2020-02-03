CREATE DATABASE 1_apiPHP;
USE 1_apiPHP;


CREATE TABLE users(
    id          int(255) auto_increment not null,
    name        varchar(50) not null ,
    surname     varchar(100),
    role        varchar(20),
    email       varchar(255) not null ,
    password    varchar(255) not null,
    description text,
    image       varchar(255),
    created_at  timestamp,
    updated_at  timestamp,
    remember_token  varchar(255),
    CONSTRAINT pk_users PRIMARY KEY(id),
    CONSTRAINT uq_email UNIQUE(email)
);

INSERT INTO users VALUES(NULL, "admin", "admin", "role_admin", "admin@admin.com", "1036400564", "this is the admin", null, CURDATE() , CURDATE(), null);

CREATE TABLE categories(
      id          int(255) auto_increment not null,
      name        varchar(100) not null ,
      created_at  timestamp,
      updated_at  timestamp,
      CONSTRAINT pk_categories PRIMARY KEY(id)
);

INSERT INTO categories VALUES(null, "ordenadores", CURDATE(),CURDATE());
INSERT INTO categories VALUES(null, "moviles", CURDATE(),CURDATE());

CREATE TABLE posts(
      id          int(255) auto_increment not null,
      user_id     int(255) not null,
      category_id int(255) not null ,
      title       varchar(255) not null ,
      content     text not null ,
      image       varchar(255),
      created_at  timestamp,
      updated_at  timestamp,
      CONSTRAINT pk_posts PRIMARY KEY(id),
      CONSTRAINT fk_posts_users FOREIGN KEY(user_id) REFERENCES users(id),
      CONSTRAINT fk_posts_categories FOREIGN KEY(category_id) REFERENCES categories(id)
);

INSERT INTO posts VALUES(null, 1, 2, "samsung galaxy s8", "galaxys are good", null, CURDATE(), CURDATE());
INSERT INTO posts VALUES(null, 1, 1, "MAC", "MACs are expensives", null, CURDATE(), CURDATE());
INSERT INTO posts VALUES(null, 1, 1, "axus", "axus are cheaps", null, CURDATE(), CURDATE());
