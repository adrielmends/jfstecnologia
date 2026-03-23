import pymysql
try:
    conn = pymysql.connect(host='localhost', user='root', password='', database='exen_exenvios', cursorclass=pymysql.cursors.DictCursor)
    with conn.cursor() as cursor:
        cursor.execute("SELECT * FROM orders ORDER BY id DESC LIMIT 5")
        result = cursor.fetchall()
        for row in result:
            print(row)
except Exception as e:
    import mysql.connector
    try:
        conn = mysql.connector.connect(host='localhost', user='root', password='', database='exen_exenvios')
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT * FROM orders ORDER BY id DESC LIMIT 5")
        result = cursor.fetchall()
        for row in result:
            print(row)
    except Exception as e2:
        print(f"Error connecting: {e}")
        print(f"Error connecting 2: {e2}")
