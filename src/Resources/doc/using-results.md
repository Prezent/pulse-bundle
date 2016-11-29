Using results
=============

When you query for aggregate results you get a `ResultSet` object.

```php
use Prezent\PulseBundle\Query\Query;

$query = new Query();
$repository = $container->get('prezent_pulse.repository');

$repository->query($query);
```

The result set is iterable and returns all aggregate rows. every row is also iterable and
returns the type name as key, and the total count as value:

```php
foreach ($result as $row) {
    echo 'Aggregate key: ' . $row->getKey() . "\n";
    echo 'Aggregate ID: ' . $row->getId() . "\n";

    foreach ($row as $column => $count) {
        echo $column . ': ' . $count . "\n";
    }

    echo 'Total: ' . $row->getTotal() . "\n";
}
```

The result set also has all column names, the totals for evey column, and the grant total of the
entire result set:

```php
$result->getColumns(); // ['type.1', 'type.2', ...]

foreach ($result->getColumnTotals() as $column => $total) {
    echo $column . ' total: ' . $total . "\n";
}

echo 'Grand total: ' . $result->getTotal();
```

Here is an example twig template for generating a results table:

```
{% extends '::base.html.twig' %}

{% block body %}
    <table>
        <thead>
            <tr>
                <th></th>
                {% for column in result.columns %}
                    <th>{{ column }}</th>
                {% endfor %}
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            {% for row in result %}
                <tr>
                    <td>{{ row.key }}: {{ row.id }}</td>
                    {% for value in row %}
                        <td>{{ value }}</td>
                    {% endfor %}
                    <td>{{ row.total }}</td>
                </tr>
            {% endfor %}
        </tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                {% for value in result.columnTotals %}
                    <td>{{ value }}</td>
                {% endfor %}
                <td>{{ result.total }}</td>
            </tr>
        </tfoot>
    </table>
{% endblock %}
```
