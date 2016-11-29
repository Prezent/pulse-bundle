<?php

namespace Prezent\PulseBundle\Command;

use Prezent\PulseBundle\Event\PulseEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Replay all events
 *
 * @see Command
 * @author Sander Marechal
 */
class ReplayCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('pulse:replay')
            ->setDescription('Clear aggregates and replay all events')
            ->setHelp('Clear aggregates and replay all events')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler('pulse:replay');

        if (!$lock->lock()) {
            $output->writeln('<error>Another instance of this command is already running.</error>');
            return 1;
        }

        $this->getContainer()->get('prezent_pulse.repository')->replayAgggregates();
    }
}
