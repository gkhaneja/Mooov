CREATE TABLE rlog (
 `id` INT NOT NULL AUTO_INCREMENT,
  service VARCHAR(200),
  method VARCHAR(200),
  arguments VARCHAR(1000),
  url VARCHAR(1000),
  response VARCHAR(2000),
  serve_time FLOAT,
  time TIMESTAMP NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id)
);
