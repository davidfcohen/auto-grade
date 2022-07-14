DROP TABLE IF EXISTS RESULT;
DROP TABLE IF EXISTS ATTEMPT_QUESTION;
DROP TABLE IF EXISTS ATTEMPT;
DROP TABLE IF EXISTS EXAM_QUESTION;
DROP TABLE IF EXISTS EXAM;
DROP TABLE IF EXISTS TEST_CASE;
DROP TABLE IF EXISTS QUESTION;
DROP TABLE IF EXISTS TEACHER;
DROP TABLE IF EXISTS STUDENT;
DROP TABLE IF EXISTS ACCOUNT;

CREATE TABLE ACCOUNT (
    account_id      INT             AUTO_INCREMENT,
    username        VARCHAR(15)     NOT NULL UNIQUE,
    first_name      VARCHAR(63)     NOT NULL,
    last_name       VARCHAR(63)     NOT NULL,
    password_hash   VARCHAR(255)    NOT NULL,
    created         DATETIME        DEFAULT CURRENT_TIMESTAMP,         
    PRIMARY KEY (account_id)
);

CREATE TABLE STUDENT (
    student_id      INT     AUTO_INCREMENT,
    account_id      INT     NOT NULL,
    PRIMARY KEY (student_id),
    FOREIGN KEY (account_id) REFERENCES ACCOUNT (account_id)
);

CREATE TABLE TEACHER (
    teacher_id      INT     AUTO_INCREMENT,
    account_id      INT     NOT NULL,
    PRIMARY KEY (teacher_id),
    FOREIGN KEY (account_id) REFERENCES ACCOUNT (account_id)
);

CREATE TABLE QUESTION (
    question_id     INT                 AUTO_INCREMENT,
    teacher_id      INT                 NOT NULL,
    topic           VARCHAR(63)         NOT NULL,
    difficulty      ENUM('EASY', 
                         'NORMAL', 
                         'HARD')        NOT NULL,
    function_name   VARCHAR(63)         NOT NULL,
    function_type   ENUM('FOR',
                         'WHILE',
                         'RECURSION'),
    content         VARCHAR(4097)       NOT NULL,
    solution        VARCHAR(4097)       NOT NULL,
    created         DATETIME            DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (question_id),
    FOREIGN KEY (teacher_id) REFERENCES TEACHER (teacher_id)
);

CREATE TABLE TEST_CASE (
    test_case_id    INT             AUTO_INCREMENT,
    question_id     INT             NOT NULL,
    driver          VARCHAR(1023)   NOT NULL,
    result          VARCHAR(1023)   NOT NULL,
    PRIMARY KEY (test_case_id),
    FOREIGN KEY (question_id) REFERENCES QUESTION (question_id) 
        ON DELETE CASCADE
);

CREATE TABLE EXAM (
    exam_id     INT         AUTO_INCREMENT,
    teacher_id  INT         NOT NULL,
    title       VARCHAR(63) NOT NULL,
    created     DATETIME    DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (exam_id),
    FOREIGN KEY (teacher_id) REFERENCES TEACHER (teacher_id)
);

CREATE TABLE EXAM_QUESTION (
    exam_id         INT     NOT NULL,
    question_id     INT     NOT NULL,
    points          FLOAT   NOT NULL,
    PRIMARY KEY (exam_id, question_id),
    FOREIGN KEY (exam_id) REFERENCES EXAM (exam_id)
        ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES QUESTION (question_id)
);

CREATE TABLE ATTEMPT (
    attempt_id  INT             AUTO_INCREMENT,
    student_id  INT             NOT NULL,     
    exam_id     INT             NOT NULL,
    comment     VARCHAR(255),
    released    TINYINT(1)      DEFAULT 0,
    created     DATETIME        DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (attempt_id),
    FOREIGN KEY (student_id) REFERENCES STUDENT (student_id),
    FOREIGN KEY (exam_id) REFERENCES EXAM (exam_id)
);

CREATE TABLE ATTEMPT_QUESTION (
    attempt_id          INT             NOT NULL,
    question_id         INT             NOT NULL,
    exam_id             INT             NOT NULL,
    grade_header        FLOAT,
    grade_constraint    FLOAT,
    grade_test_case     FLOAT,
    comment             VARCHAR(255),
    content             VARCHAR(4097)   NOT NULL,    
    PRIMARY KEY (attempt_id, question_id, exam_id),
    FOREIGN KEY (attempt_id) REFERENCES ATTEMPT (attempt_id),
    FOREIGN KEY (question_id) REFERENCES EXAM_QUESTION (question_id),
    FOREIGN KEY (exam_id) REFERENCES EXAM_QUESTION (exam_id)  
);

CREATE TABLE RESULT (
    attempt_id      INT         NOT NULL,
    question_id     INT         NOT NULL,
    exam_id         INT         NOT NULL,
    test_case_id    INT         NOT NULL,
    passed          TINYINT(1)  DEFAULT 0,
    PRIMARY KEY (attempt_id, question_id, exam_id, test_case_id),
    FOREIGN KEY (attempt_id) REFERENCES ATTEMPT_QUESTION (attempt_id),
    FOREIGN KEY (question_id) REFERENCES ATTEMPT_QUESTION (question_id),
    FOREIGN KEY (exam_id) REFERENCES ATTEMPT_QUESTION (exam_id),
    FOREIGN KEY (test_case_id) REFERENCES TEST_CASE (test_case_id)
);


INSERT INTO ACCOUNT (username, first_name, last_name, password_hash) 
    VALUES ('student1', 'David', 'Cohen',
        '$2y$10$G9whZTF5uKQ5qXK1Ual.OuihxKKJh/6oeW03VdXb8K6dwrM8xlP0W');
INSERT INTO ACCOUNT (username, first_name, last_name, password_hash)
    VALUES ('student2', 'Akimori', 'Ino',
        '$2y$10$G9whZTF5uKQ5qXK1Ual.OuihxKKJh/6oeW03VdXb8K6dwrM8xlP0W');
INSERT INTO ACCOUNT (username, first_name, last_name, password_hash)
    VALUES ('student3', 'Nikhil', 'Ramesh',
        '$2y$10$G9whZTF5uKQ5qXK1Ual.OuihxKKJh/6oeW03VdXb8K6dwrM8xlP0W');
INSERT INTO ACCOUNT (username, first_name, last_name, password_hash) 
    VALUES ('teacher1', 'Theodore', 'Nicholson', 
        '$2y$10$oK9kZEQ2cOJFWRhI0lbtw.Sthhi5B5VHUE/3wp00cQQ3J477JOoNu');
INSERT INTO STUDENT (account_id)
    VALUES (1);
INSERT INTO STUDENT (account_id)
    VALUES (2);
INSERT INTO STUDENT (account_id)
    VALUES (3);
INSERT INTO TEACHER (account_id)
    VALUES (4);


INSERT INTO QUESTION(teacher_id, topic, difficulty,
    function_name, content, solution)
    VALUES (1, 'Functions', 'EASY','double_it(x)',
        'Write function double_it(x) that returns twice the value of x.',
        'def double_it(x):
            return 2 * x');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (1, 'print(double_it(2))', '4');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (1, 'print(double_it(16))', '32');

INSERT INTO QUESTION(teacher_id, topic, difficulty,
    function_name, content, solution)
    VALUES (1, 'Functions', 'EASY','is_even(x)',
        'Write function is_even that returns 1 if x is even or 0 if x is odd.',
        'def is_even(x):
            return x % 2');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (2, 'print(is_even(64))', 'True');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (2, 'print(is_even(255))', 'False');

INSERT INTO QUESTION(teacher_id, topic, difficulty,
    function_name, function_type, content, solution)
    VALUES (1, 'Loops', 'NORMAL','sum_it(A)', 'FOR', 
        'Write function sum_it(A) returns the sum of all elements in A using a for loop.',
        'def sum_it(A):
            sum = 0
            for i in A:
                sum += i
            return sum');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (3, 
        'A = [10, 20, 30]
            print(sum_it(A))', '60');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (3,
        'A = [2, 4, 8, 16]
            print(sum_it(A))', '30');

INSERT INTO QUESTION(teacher_id, topic, difficulty,
    function_name, function_type, content, solution)
    VALUES (1, 'Recursion', 'HARD', 'fibonacci(n)', 'RECURSION',
        'Write a recursive function fibonacci(n) that returns the last value in a fibonacci sequence of size n.',
        'def fibonacci(n):
            if n <= 1:
                return n
            else:
                return(fibonacci(n-1) + fibonacci(n-2))');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (4, 'print(fibonacci(2))', '2');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (4, 'print(fibonacci(8))', '128');

INSERT INTO QUESTION(teacher_id, topic, difficulty,
    function_name, content, solution)
    VALUES (1, 'Functions', 'NORMAL',
        'operation(op, a, b)',
        'Write a function that takes 3 parameters (op, a, b) that returns
            a + b if op is +
            a - b if op is -
            a * b if op is *
            a / b if op is /
            -1 otherwise',
        'def operation(op, a, b):
            if op == "+":
                return a + b
            elif op == "-":
                return a - b
            elif op == "*":
                return a * b
            elif op == "/":
                return a / b
            else:
                return -1');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (5, 'print(operation(+, 5, 2))', '7');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (5, 'print(operation(-, 10, 12))', '-2');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (5, 'print(operation(*, 4, 4))', '16');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (5, 'print(operation(/, 12, 4))', '3');
INSERT INTO TEST_CASE(question_id, driver, result)
    VALUES (5, 'print(operation(&, 5, 2))', '-1');


INSERT INTO EXAM(teacher_id, title)
    VALUES (1, 'CS100 Midterm');
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (1, 1, 10);
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (1, 3, 10);
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (1, 5, 20);

INSERT INTO EXAM(teacher_id, title)
    VALUES (1, 'CS100 Final');
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (2, 2, 10);
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (2, 4, 10);
INSERT INTO EXAM_QUESTION(exam_id, question_id, points)
    VALUES (2, 5, 20);


INSERT INTO ATTEMPT(student_id, exam_id)
    VALUES (1, 1);
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (1, 1, 1, 
        'def double_it(x):
            return 2 * x');
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (1, 3, 1, 
        'def sum_it(A):
            sum = 0
            for i in A:
                sum += i
            return sum');
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (1, 5, 1,
        'def operation(op, a, b):
            if op == "+":
                return a + b
            elif op == "-":
                return a - b
            elif op == "*":
                return a * b
            elif op == "/":
                return a / b
            else:
                return -1');

INSERT INTO ATTEMPT(student_id, exam_id)
    VALUES (2, 2);
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (2, 2, 2, 
        'def is_even(x):
            return x % 2');
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (2, 4, 2, 
        'def fibonacci(n):
            if n <= 1:
                return n
            else:
                return(fibonacci(n-1) + fibonacci(n-2))');
INSERT INTO ATTEMPT_QUESTION(attempt_id, question_id, exam_id, content)
    VALUES (2, 5, 2,
        'def operation(op, a, b):
            if op == "+":
                return a + b
            elif op == "-":
                return a - b
            elif op == "*":
                return a ** b
            elif op == "/":
                return a / b
            else:
                return 0');
