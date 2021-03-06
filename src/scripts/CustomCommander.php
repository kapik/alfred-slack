<?php namespace Frlnc\Slack\Core;

use InvalidArgumentException;
use Frlnc\Slack\Contracts\Http\Interactor;

class CustomCommander extends \Frlnc\Slack\Core\Commander {

    /**
     * Executes a command.
     *
     * @param  string $command
     * @param  array $parameters
     * @return \Frlnc\Slack\Contracts\Http\Response
     */
    public function executeAll($commandsWithParameters)
    {
        $requests = [];
        foreach ($commandsWithParameters as $commandWithParameters) {
            $command = $commandWithParameters['command'];
            if (!empty($commandWithParameters['parameters'])) {
                $parameters = $commandWithParameters['parameters'];
            } else {
                $parameters = [];
            }

            if (!isset(self::$commands[$command]))
                throw new InvalidArgumentException("The command '{$command}' is not currently supported");

            $command = self::$commands[$command];

            if ($command['token'])
                $parameters = array_merge($parameters, ['token' => $this->token]);

            if (isset($command['format']))
                foreach ($command['format'] as $format)
                    if (isset($parameters[$format]))
                        $parameters[$format] = self::format($parameters[$format]);

            $headers = [];
            if (isset($command['headers']))
                $headers = $command['headers'];

            $url = self::$baseUrl . $command['endpoint'];

            if (isset($command['post']) && $command['post'])
                return $this->interactor->post($url, [], $parameters, $headers);
            
            $requests[] = [ 'url' => $url, 'parameters' => $parameters, 'headers' => $headers ];
        }

        return $this->interactor->getAll($requests);
    }

}
