<?php namespace BackupManager\Laravel;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * Class AutoComplete
 *
 * @package BackupManager\Laravel
 */
trait AutoComplete {
    /**
     * @param $dialog
     * @param array $list
     * @param null $default
     * @throws \LogicException
     * @throws InvalidArgumentException
     * @internal param $question
     * @return mixed
     */
    public function autocomplete($dialog, array $list, $default = null) {
        $validation = function ($item) use ($list) {
            if ( ! in_array($item, array_values($list))) {
                throw new \InvalidArgumentException("{$item} does not exist.");
            }
            return $item;
        };

        try {
            return $this->useSymfontDialog($dialog, $list, $validation, $default);
        } catch (InvalidArgumentException $error) {
            //
        }
        return $this->useSymfonyQuestion($dialog, $validation, $default);
    }

    /**
     * @param $dialog
     * @param array $list
     * @param null $default
     * @return mixed
     */
    protected function useSymfontDialog($dialog, array $list, $validation, $default = null) {
        $helper = $this->getHelperSet()->get('dialog');

        return $helper->askAndValidate(
            $this->output, "<question>{$dialog}</question>", $validation, false, $default, $list
        );
    }

    /**
     * @param $dialog
     * @param null $default
     * @return mixed
     */
    protected function useSymfonyQuestion($dialog, $validation, $default = null) {
        $question = new Question($dialog . ' ', $default);
        $question->setValidator($validation);
        $helper = $this->getHelper('question');

        return $helper->ask($this->input, $this->output, $question);
    }
}
