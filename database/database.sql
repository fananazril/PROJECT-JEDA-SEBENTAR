CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50),
    email VARCHAR(50),
    password VARCHAR(25),
    PRIMARY KEY (id)
);

CREATE TABLE jurnal (
    idjurnal INT NOT NULL AUTO_INCREMENT,
    id_user INT,
    judul VARCHAR(100),
    isi TEXT,
    tanggal DATE,
    dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idjurnal),
    CONSTRAINT fk_user_jurnal FOREIGN KEY (id_user) REFERENCES user(id)
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);