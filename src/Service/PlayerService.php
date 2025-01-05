<?php
namespace App\Service;

use App\Entity\Players;
use App\Repository\PlayersRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlayerService
{
    private $playerRepository;
    private $entityManager;

    public function __construct(PlayersRepository $playerRepository, EntityManagerInterface $entityManager)
    {
        $this->playerRepository = $playerRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllPlayers()
    {
        return $this->playerRepository->getAllPlayers();
    }

    public function addPlayer(string $name): Players
    {
        $player = new Players();
        $player->setName($name);
        $player->setScore(0);  // Initialize new player with 0 score
        $player->setLastGames([]);  // Initialize with empty array

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $player;
    }

    public function addPoints(int $points, string $playerName, bool $didPlay = true): void
    {
        // Find the player by name
        $player = $this->playerRepository->findByName($playerName);

        if (!$player) {
            throw new \Exception('Player not found');
        }

        if ($didPlay) {
            // If the player played, update their score
            $playerScore = $player->getScore();
            $finalScore = $playerScore + $points;
            $player->setScore($finalScore);
        }

        // Update the LastGames history with the participation status
        $gamesArray = $player->getLastGames();

        // If the player didn't play, record 'x'; otherwise, record the points
        $gamesArray[] = [
            'points' => $didPlay ? $player->getScore() : 'x',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ];

        $player->setLastGames($gamesArray);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }
}