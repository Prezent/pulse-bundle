prezent/pulse-bundle
====================

Basic analytics event tracking in Symfony

The full documentation can be found in [Resources/doc](src/Resources/doc/index.md)

Quickstart example
------------------

```php
namespace AppBundle\Controller;

use Prezent\PulseBundle\Event\PulseEvent;
use Prezent\PulseBundle\Query\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller

class AppController extends Controller
{
    public function trackableAction()
    {
        $this->get('event_dispatcher')->dispatch(PulseEvent::EVENT, new PulseEvent('some.action', [
            'user' => $this->getUser()->getId(),
        ]));
    }

    public function analyticsAction()
    {
        $query = new Query();
        $query
            ->setTypes(['some.action', 'other.action'])
            ->setStartDate(new \DateTime('-7 days'))
            ->setEndDate(new \DateTime())
        ;

        return $this->render('AppBundle:App:analytics.html.twig', [
            'result' => $this->get('prezent_pulse.repository')->query($query),
        ]);
    }
}
```

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
