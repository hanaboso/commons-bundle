<?php declare(strict_types=1);

namespace Tests\Integration\Database\Repository;

use Exception;
use Hanaboso\CommonsBundle\Database\Document\Node;
use Hanaboso\CommonsBundle\Database\Document\Topology;
use Hanaboso\CommonsBundle\Database\Reduction\NodeReduction;
use Hanaboso\CommonsBundle\Database\Repository\NodeRepository;
use Hanaboso\CommonsBundle\Enum\HandlerEnum;
use Hanaboso\CommonsBundle\Enum\TypeEnum;
use LogicException;
use Tests\DatabaseTestCaseAbstract;

/**
 * Class NodeRepositoryTest
 *
 * @package Tests\Integration\Database\Repository
 */
final class NodeRepositoryTest extends DatabaseTestCaseAbstract
{

    /**
     * @covers NodeRepository::getEventNodesByTopology()
     * @throws Exception
     */
    public function testGetEventNodesByTopology(): void
    {
        /** @var NodeRepository $repo */
        $repo = $this->dm->getRepository(Node::class);

        $result = $repo->getEventNodesByTopology('abc123');

        self::assertEmpty($result);

        $node1 = new Node();
        $node1->setName('name 1');
        $node1->setType(TypeEnum::CONNECTOR);
        $node1->setTopology('abc123');

        $node2 = new Node();
        $node2->setName('name 2');
        $node2->setType(TypeEnum::EMAIL);
        $node2->setTopology('abc123');

        $this->dm->persist($node1);
        $this->dm->persist($node2);
        $this->dm->flush();

        NodeReduction::$typeExclude = [];
        $result                     = array_values($repo->getEventNodesByTopology('abc123'));
        self::assertCount(2, $result);
        self::assertEquals($node1, $result[0]);
        self::assertEquals($node2, $result[1]);

        NodeReduction::$typeExclude = [TypeEnum::EMAIL];
        $result                     = array_values($repo->getEventNodesByTopology('abc123'));
        self::assertCount(1, $result);
        self::assertEquals($node1, $result[0]);

        NodeReduction::$typeExclude = [TypeEnum::CONNECTOR];
        $result                     = array_values($repo->getEventNodesByTopology('abc123'));
        self::assertCount(1, $result);
        self::assertEquals($node2, $result[0]);

        NodeReduction::$typeExclude = [TypeEnum::EMAIL, TypeEnum::CONNECTOR];
        $result                     = array_values($repo->getEventNodesByTopology('abc123'));
        self::assertCount(0, $result);
    }

    /**
     * @covers NodeRepository::getNodeByTopology
     * @throws Exception
     */
    public function testGetNodeByTopology(): void
    {
        /** @var NodeRepository $repo */
        $repo   = $this->dm->getRepository(Node::class);
        $result = $repo->getNodeByTopology('name1', 'abc123');
        self::assertEmpty($result);

        $node1 = new Node();
        $node1
            ->setEnabled(TRUE)
            ->setName('name1')
            ->setTopology('abc123');

        $this->dm->persist($node1);
        $this->dm->flush();

        $result = $repo->getNodeByTopology('name1', 'abc123');
        self::assertNotEmpty($result);
    }

    /**
     * @covers NodeRepository::getStartingNode
     * @throws Exception
     */
    public function testGetStartingPoint(): void
    {
        /** @var NodeRepository $repo */
        $repo = $this->dm->getRepository(Node::class);

        $topology = new Topology();
        $this->dm->persist($topology);
        $this->dm->flush($topology);

        $node = new Node();
        $node
            ->setEnabled(TRUE)
            ->setTopology($topology->getId())
            ->setType(TypeEnum::SIGNAL)
            ->setHandler(HandlerEnum::EVENT);
        $this->dm->persist($node);
        $this->dm->flush($node);
        $this->dm->clear();

        self::assertEquals($node->getId(), $repo->getStartingNode($topology)->getId());
    }

    /**
     * @covers NodeRepository::getStartingNode
     * @throws Exception
     */
    public function testGetStartingPointNotFound(): void
    {
        /** @var NodeRepository $repo */
        $repo = $this->dm->getRepository(Node::class);

        $topology = new Topology();
        $this->dm->persist($topology);
        $this->dm->flush($topology);

        $node = new Node();
        $node
            ->setEnabled(TRUE)
            ->setTopology($topology->getId())
            ->setType(TypeEnum::MAPPER)
            ->setHandler(HandlerEnum::EVENT);
        $this->dm->persist($node);
        $this->dm->flush($node);
        $this->dm->clear();

        self::expectException(LogicException::class);
        self::expectExceptionMessage(sprintf('Starting Node not found for topology [%s]', $topology->getId()));
        $repo->getStartingNode($topology);
    }

    /**
     * @covers NodeRepository::getTopologyType
     * @throws Exception
     */
    public function testGetTopologyType(): void
    {
        /** @var NodeRepository $repo */
        $repo = $this->dm->getRepository(Node::class);

        $topology = new Topology();
        $this->dm->persist($topology);
        $this->dm->flush($topology);

        $node = new Node();
        $node
            ->setEnabled(TRUE)
            ->setTopology($topology->getId())
            ->setType(TypeEnum::CRON)
            ->setHandler(HandlerEnum::EVENT);
        $this->dm->persist($node);
        $this->dm->flush($node);
        $this->dm->clear();

        $type = $repo->getTopologyType($topology);
        self::assertEquals(TypeEnum::CRON, $type);

        $topology = new Topology();
        $this->dm->persist($topology);
        $this->dm->flush($topology);

        $node = new Node();
        $node
            ->setEnabled(TRUE)
            ->setTopology($topology->getId())
            ->setType(TypeEnum::CONNECTOR)
            ->setHandler(HandlerEnum::EVENT);
        $this->dm->persist($node);
        $this->dm->flush($node);
        $this->dm->clear();

        $type = $repo->getTopologyType($topology);
        self::assertEquals(TypeEnum::WEBHOOK, $type);
    }

    /**
     * @covers NodeRepository::getNodesByTopology
     * @throws Exception
     */
    public function testGetNodesByTopology(): void
    {
        /** @var NodeRepository $repo */
        $repo = $this->dm->getRepository(Node::class);

        $topology = new Topology();
        $this->dm->persist($topology);
        $this->dm->flush($topology);

        $node = new Node();
        $node
            ->setEnabled(TRUE)
            ->setTopology($topology->getId())
            ->setType(TypeEnum::MAPPER)
            ->setHandler(HandlerEnum::EVENT);
        $this->dm->persist($node);
        $this->dm->flush($node);
        $this->dm->clear();

        /** @var Node[] $nodes */
        $nodes = $repo->getNodesByTopology($topology->getId());
        self::assertCount(1, $nodes);
        /** @var Node $first */
        $first = reset($nodes);
        self::assertEquals($node->getId(), $first->getId());
    }

}
