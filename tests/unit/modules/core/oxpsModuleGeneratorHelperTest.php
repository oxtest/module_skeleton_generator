<?php
/**
 * This file is part of OXID Module Skeleton Generator module.
 *
 * OXID Module Skeleton Generator module is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * OXID Module Skeleton Generator module is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Module Skeleton Generator module.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       modulegenerator
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Class oxpsModuleGeneratorHelperTest
 * UNIT tests for core class oxpsModuleGeneratorHelper.
 *
 * @see oxpsModuleGeneratorHelper
 */
class oxpsModuleGeneratorHelperTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModuleGeneratorHelper
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModuleGeneratorHelper', array('__call', '_shellExec'));
    }


    public function testInit()
    {
        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        $this->SUT->init($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    public function testGetModule()
    {
        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call'));

        $this->SUT->setModule($oModule);

        $this->assertSame($oModule, $this->SUT->getModule());
    }


    public function testGetFileSystemHelper()
    {
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call'));
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        $this->assertSame($oFileSystem, $this->SUT->getFileSystemHelper());
    }


    public function testCreateVendorMetadata()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'createFolder', 'createFile'));
        $oFileSystem->expects($this->once())->method('createFolder')->with('/path/to/modules/oxps');
        $oFileSystem->expects($this->once())->method('createFile')->with(
            '/path/to/modules/oxps/vendormetadata.php',
            '<?php' . PHP_EOL . PHP_EOL .

            '/**' . PHP_EOL .
            ' * Metadata version' . PHP_EOL .
            ' */' . PHP_EOL .
            '$sVendorMetadataVersion = \'1.0\';' . PHP_EOL
        );
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        $this->SUT->createVendorMetadata('/path/to/modules/oxps');
    }


    public function testCreateClassesToExtend_invalidTemplatePath_returnEmptyArray()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->once())->method('isFile')->with('/path/to/template.tpl')->will(
            $this->returnValue(false)
        );
        $oFileSystem->expects($this->never())->method('isDir');
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToExtend', 'getFullPath', 'getModuleId')
        );
        $oModule->expects($this->once())->method('getClassesToExtend')->will(
            $this->returnValue(
                array(
                    'oxarticle' => 'models/',
                    'oxList'    => 'core/',
                    'nonClass'  => 'core/',
                )
            )
        );
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->once())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));

        $this->SUT->init($oModule);

        $this->assertSame(array(), $this->SUT->createClassesToExtend('/path/to/template.tpl'));
    }

    public function testCreateClassesToExtend_moduleFolderDoesNotExist_returnEmptyArray()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->once())->method('isFile')->with('/path/to/template.tpl')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->once())->method('isDir')->with('/path/to/modules/oxps/mymodule/')->will(
            $this->returnValue(false)
        );
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToExtend', 'getFullPath', 'getModuleId')
        );
        $oModule->expects($this->once())->method('getClassesToExtend')->will(
            $this->returnValue(
                array(
                    'oxarticle' => 'models/',
                    'oxList'    => 'core/',
                    'nonClass'  => 'core/',
                )
            )
        );
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->once())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));

        $this->SUT->init($oModule);

        $this->assertSame(array(), $this->SUT->createClassesToExtend('/path/to/template.tpl'));
    }

    public function testCreateClassesToExtend_noClassesToExtend_returnEmptyArray()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->once())->method('isFile')->with('/path/to/template.tpl')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->once())->method('isDir')->with('/path/to/modules/oxps/mymodule/')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToExtend', 'getFullPath', 'getModuleId')
        );
        $oModule->expects($this->once())->method('getClassesToExtend')->will($this->returnValue(array()));
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->once())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));

        $this->SUT->init($oModule);

        $this->assertSame(array(), $this->SUT->createClassesToExtend('/path/to/template.tpl'));
    }

    public function testCreateClassesToExtend_templateAndModuleDirAndClassesToExtendAreValid_returnCreatedClassesArray()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->at(0))->method('isFile')->with('/path/to/template.tpl')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->at(1))->method('isDir')->with('/path/to/modules/oxps/mymodule/')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->at(2))->method('isDir')->with('/path/to/modules/oxps/mymodule/Model/')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->at(3))->method('copyFile')->with(
            '/path/to/template.tpl',
            '/path/to/modules/oxps/mymodule/Model/oxpsmymoduleoxarticle.php'
        );
        $oFileSystem->expects($this->at(4))->method('isDir')->with('/path/to/modules/oxps/mymodule/Core/')->will(
            $this->returnValue(true)
        );
        $oFileSystem->expects($this->at(5))->method('copyFile')->with(
            '/path/to/template.tpl',
            '/path/to/modules/oxps/mymodule/Core/oxpsmymoduleoxList.php'
        );
        $oFileSystem->expects($this->at(6))->method('isDir')->with('/path/to/modules/oxps/mymodule/faulty_dir/')->will(
            $this->returnValue(false)
        );
        $oFileSystem->expects($this->at(7))->method('copyFile')->with(
            '/path/to/template.tpl',
            '/path/to/modules/oxps/mymodule/Core/oxpsmymodulenonClass.php'
        );
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToExtend', 'getFullPath', 'getModuleId')
        );
        $oModule->expects($this->once())->method('getClassesToExtend')->will(
            $this->returnValue(
                array(
                    'oxarticle' => 'Model/',
                    'oxList'    => 'Core/',
                    'nonClass'  => 'faulty_dir/',
                )
            )
        );
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->once())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));

        $this->SUT->init($oModule);

        $this->assertSame(
            array(
                'Model/oxpsmymoduleoxarticle.php' => 'oxArticle',
                'Core/oxpsmymoduleoxList.php'      => 'oxList',
                'Core/oxpsmymodulenonClass.php'    => 'nonClass',
            ),
            $this->SUT->createClassesToExtend('/path/to/template.tpl')
        );
    }


    public function testCreateNewClassesAndTemplates_noClassesData_returnEmptyArray()
    {
        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToCreate')
        );
        $oModule->expects($this->once())->method('getClassesToCreate')->will($this->returnValue(array()));

        $this->SUT->setModule($oModule);

        $this->assertSame(array(), $this->SUT->createNewClassesAndTemplates('/path/to/modules/oxps/modulegenerator'));
    }

    public function testCreateNewClassesAndTemplates_noClassesToCreate_returnEmptyArray()
    {
        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToCreate')
        );
        $oModule->expects($this->once())->method('getClassesToCreate')->will(
            $this->returnValue(
                array(
                    'widgets'     => array(
                        'aClasses'       => array(),
                        'sTemplateName'  => 'oxpswidgetclass.php.tpl',
                        'sInModulePath'  => 'components/widgets/',
                        'sTemplatesPath' => 'widgets',
                    ),
                    'controllers' => array(
                        'aClasses'       => array(),
                        'sTemplateName'  => 'oxpscontrollerclass.php.tpl',
                        'sInModulePath'  => 'controllers/',
                        'sTemplatesPath' => 'pages',
                    ),
                    'models'      => array(
                        'aClasses'      => array(),
                        'sTemplateName' => 'oxpsmodelclass.php.tpl',
                        'sInModulePath' => 'models/',
                    ),
                    'list_models' => array(
                        'aClasses'      => array(),
                        'sTemplateName' => 'oxpslistmodelclass.php.tpl',
                        'sInModulePath' => 'models/',
                    ),
                )
            )
        );

        $this->SUT->setModule($oModule);

        $this->assertSame(array(), $this->SUT->createNewClassesAndTemplates('/path/to/modules/oxps/modulegenerator'));
    }

    public function testCreateNewClassesAndTemplates_thereAreClassesToCreate_returnCreatedClassesArray()
    {
        // File system helper mock
        $oFileSystem = $this->getMock(
            'oxpsModuleGeneratorFileSystem',
            array('__call', 'isFile', 'isDir', 'copyFile', 'createFile')
        );

        // For faulty items "Faulty Class"
        $oFileSystem->expects($this->at(0))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/faulty.php.tpl')
            ->will($this->returnValue(false));

        // For a widget "Bar" class and template
        $oFileSystem->expects($this->at(1))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsWidgetClass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(2))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/components/widgets/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(3))->method('copyFile')
            ->with(
                '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsWidgetClass.php.tpl',
                '/path/to/modules/oxps/mymodule/components/widgets/oxpsMyModuleBar.php'
            );
        $oFileSystem->expects($this->at(4))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/Application/views/widgets/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(5))->method('createFile')
            ->with(
                '/path/to/modules/oxps/mymodule/Application/views/widgets/oxpsMyModuleBar.tpl',
                $this->stringContains('oxpsMyModuleBar')
            );

        // For a controller "Page" class and template
        $oFileSystem->expects($this->at(6))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsControllerClass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(7))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/controllers/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(8))->method('copyFile')
            ->with(
                '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsControllerClass.php.tpl',
                '/path/to/modules/oxps/mymodule/controllers/oxpsMyModulePage.php'
            );
        $oFileSystem->expects($this->at(9))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/Application/views/pages/')
            ->will($this->returnValue(false));

        // For a model "Item" class
        $oFileSystem->expects($this->at(10))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsModelClass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(11))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/models/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(12))->method('copyFile')
            ->with(
                '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsModelClass.php.tpl',
                '/path/to/modules/oxps/mymodule/models/oxpsMyModuleItem.php'
            );

        // For a model "Thing" class
        $oFileSystem->expects($this->at(13))->method('copyFile')
            ->with(
                '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsModelClass.php.tpl',
                '/path/to/modules/oxps/mymodule/models/oxpsMyModuleThing.php'
            );

        // For a list model "Item" class
        $oFileSystem->expects($this->at(14))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsListModelClass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(15))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/models/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(16))->method('copyFile')
            ->with(
                '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpsListModelClass.php.tpl',
                '/path/to/modules/oxps/mymodule/models/oxpsMyModuleItemList.php'
            );

        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getClassesToCreate', 'getModuleId', 'getFullPath', 'getModuleClassName')
        );
        $oModule->expects($this->once())->method('getClassesToCreate')->will(
            $this->returnValue(
                array(
                    'faulty'      => array(
                        'aClasses'       => array('Faulty Class'),
                        'sTemplateName'  => 'faulty.php.tpl',
                        'sInModulePath'  => 'faulty/',
                        'sTemplatesPath' => 'faulty',
                    ),
                    'widgets'     => array(
                        'aClasses'       => array('Bar'),
                        'sTemplateName'  => 'oxpsWidgetClass.php.tpl',
                        'sInModulePath'  => 'components/widgets/',
                        'sTemplatesPath' => 'widgets',
                    ),
                    'controllers' => array(
                        'aClasses'       => array('Page'),
                        'sTemplateName'  => 'oxpsControllerClass.php.tpl',
                        'sInModulePath'  => 'controllers/',
                        'sTemplatesPath' => 'pages',
                    ),
                    'models'      => array(
                        'aClasses'      => array('Item', 'Thing'),
                        'sTemplateName' => 'oxpsModelClass.php.tpl',
                        'sInModulePath' => 'models/',
                    ),
                    'list_models' => array(
                        'aClasses'      => array('ItemList'),
                        'sTemplateName' => 'oxpsListModelClass.php.tpl',
                        'sInModulePath' => 'models/',
                    ),
                )
            )
        );
        $oModule->expects($this->any())->method('getModuleId')->will($this->returnValue('oxpsMyModule'));
        $oModule->expects($this->any())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->any())->method('getModuleClassName')->will($this->returnValue('oxpsMyModule'));

        $this->SUT->setModule($oModule);

        $this->assertSame(
            array(
                'components/widgets/oxpsMyModuleBar.php' => 'Bar',
                'controllers/oxpsMyModulePage.php'       => 'Page',
                'models/oxpsMyModuleItem.php'            => 'Item',
                'models/oxpsMyModuleThing.php'           => 'Thing',
                'models/oxpsMyModuleItemList.php'        => 'ItemList',
            ),
            $this->SUT->createNewClassesAndTemplates('/path/to/modules/oxps/ModuleGenerator/')
        );
    }


    public function testCreateBlock_noBlocksDefined_noTemplatesCreated()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isDir', 'createFile'));
        $oFileSystem->expects($this->never())->method('isDir');
        $oFileSystem->expects($this->never())->method('createFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getBlocks', 'getModuleId', 'getFullPath', 'getModuleClassName')
        );
        $oModule->expects($this->once())->method('getBlocks')->will($this->returnValue(array()));
        $oModule->expects($this->never())->method('getModuleId');
        $oModule->expects($this->never())->method('getFullPath');
        $oModule->expects($this->never())->method('getModuleClassName');

        $this->SUT->setModule($oModule);

        $this->SUT->createBlock('/path/to/modules/oxps/mymodule/');
    }

    public function testCreateBlock_blocksAreDefined_callBlockTemplatesCreation()
    {
        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isDir', 'createFile'));
        $oFileSystem->expects($this->at(0))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/Application/views/blocks/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(1))->method('createFile')->with(
            '/path/to/modules/oxps/mymodule/Application/views/blocks/oxpsmymodule_my_block.tpl',
            $this->stringContains('my_block')
        );
        $oFileSystem->expects($this->at(2))->method('createFile')->with(
            '/path/to/modules/oxps/mymodule/Application/views/blocks/oxpsmymodule_footer.tpl',
            $this->stringContains('footer')
        );
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getBlocks', 'getModuleId', 'getFullPath', 'getModuleClassName')
        );
        $oModule->expects($this->once())->method('getBlocks')->will(
            $this->returnValue(
                array(
                    '_my_block' => array(
                        'page'     => 'page.tpl',
                        'block'    => 'my_block',
                        'template' => 'oxpsmymodule_my_block.tpl'
                    ),
                    '_footer'   => array(
                        'page'     => 'base.tpl',
                        'block'    => 'footer',
                        'template' => 'oxpsmymodule_footer.tpl'
                    ),
                )
            )
        );
        $oModule->expects($this->any())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));
        $oModule->expects($this->any())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->any())->method('getModuleClassName')->will($this->returnValue('oxpsMyModule'));

        $this->SUT->setModule($oModule);

        $this->SUT->createBlock('/path/to/modules/oxps/mymodule/');
    }


    public function testFillTestsFolder_testsGitUrlNotSet_createNoTestClasses()
    {
        // TODO: Double check if this test is needed as GIT URL was removed from method logic
        //$this->markTestSkipped('GIT URL was removed, test to be adjusted, when final v0.6.0 is prepared.'); // TODO DDR

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'renderWithSmartyAndRename'));
        $oRenderHelper->expects($this->never())->method('renderWithSmartyAndRename');

        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        $this->SUT->fillTestsFolder(
            $oRenderHelper,
            '/path/to/modules/oxps/modulegenerator/',
            //'/path/to/modules/oxps/mymodule/',
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array(
                'controllers/oxpsmymodulepage.php' => 'Page',
                'models/oxpsmymoduleitem.php'      => 'Item',
            )
        );
    }

    public function testFillTestsFolder_testsFolderNotFetched_createNoTestClasses()
    {
        $this->markTestIncomplete('Fix when tests generation is complete in v0.6.0'); // TODO DDR:

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorModule', array('__construct', '__call', 'getSetting'));
        $oModule->expects($this->once())->method('getSetting')->with('TestsGitUrl')->will(
            $this->returnValue('git@example.com:path_to/tests.git')
        );
        oxRegistry::set('oxpsModuleGeneratorModule', $oModule);

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'renderWithSmartyAndRename'));
        $oRenderHelper->expects($this->never())->method('renderWithSmartyAndRename');

        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->once())->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/')
            ->will($this->returnValue(false));
        $oFileSystem->expects($this->never())->method('isFile');
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        $this->SUT->fillTestsFolder(
            $oRenderHelper,
            '/path/to/modules/oxps/modulegenerator/',
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array(
                'controllers/oxpsmymodulepage.php' => 'Page',
                'models/oxpsmymoduleitem.php'      => 'Item',
            )
        );
    }

    public function testFillTestsFolder_noNewFiles_createNoTestClasses()
    {
        $this->markTestIncomplete('Fix when tests generation is complete in v0.6.0'); // TODO DDR:

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorModule', array('__construct', '__call', 'getSetting'));
        $oModule->expects($this->once())->method('getSetting')->with('TestsGitUrl')->will(
            $this->returnValue('git@example.com:path_to/tests.git')
        );
        oxRegistry::set('oxpsModuleGeneratorModule', $oModule);

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'renderWithSmartyAndRename'));
        $oRenderHelper->expects($this->never())->method('renderWithSmartyAndRename');

        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->at(0))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(1))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(2))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/modules/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call', 'getFullPath'));
        $oModule->expects($this->once())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));

        $this->SUT->init($oModule);

        $this->SUT->fillTestsFolder(
            $oRenderHelper,
            '/path/to/modules/oxps/modulegenerator/',
            array(),
            array()
        );
    }

    public function testFillTestsFolder_testClassTemplateIsInvalid_createNoTestClasses()
    {
        $this->markTestIncomplete('Fix when tests generation is complete in v0.6.0'); // TODO DDR:

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorModule', array('__construct', '__call', 'getSetting'));
        $oModule->expects($this->once())->method('getSetting')->with('TestsGitUrl')->will(
            $this->returnValue('git@example.com:path_to/tests.git')
        );
        oxRegistry::set('oxpsModuleGeneratorModule', $oModule);

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'renderWithSmartyAndRename'));
        $oRenderHelper->expects($this->never())->method('renderWithSmartyAndRename');

        // File system helper mock
        $oFileSystem = $this->getMock('oxpsModuleGeneratorFileSystem', array('__call', 'isFile', 'isDir', 'copyFile'));
        $oFileSystem->expects($this->at(0))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(1))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl')
            ->will($this->returnValue(false));
        $oFileSystem->expects($this->never())->method('copyFile');
        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorOxModule', array('__construct', '__call', 'getFullPath'));
        $oModule->expects($this->never())->method('getFullPath');

        $this->SUT->init($oModule);

        $this->SUT->fillTestsFolder(
            $oRenderHelper,
            '/path/to/modules/oxps/modulegenerator/',
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array(
                'controllers/oxpsmymodulepage.php' => 'Page',
                'models/oxpsmymoduleitem.php'      => 'Item',
            )
        );
    }

    public function testFillTestsFolder_testsFolderFetchedAllPathsAreValid_createAndProcessTestClasses()
    {
        $this->markTestIncomplete('Fix when tests generation is complete in v0.6.0'); // TODO DDR:

        // Module instance mock
        $oModule = $this->getMock('oxpsModuleGeneratorModule', array('__construct', '__call', 'getSetting'));
        $oModule->expects($this->once())->method('getSetting')->with('TestsGitUrl')->will(
            $this->returnValue('git@example.com:path_to/tests.git')
        );
        oxRegistry::set('oxpsModuleGeneratorModule', $oModule);

        // Render helper mock
        $oRenderHelper = $this->getMock('oxpsModuleGeneratorRender', array('__call', 'renderWithSmartyAndRename'));
        $oRenderHelper->expects($this->once())->method('renderWithSmartyAndRename')->with(
            array(
                'tests/unit/modules/models/oxpsmymoduleoxarticleTest.php',
                'tests/unit/modules/controllers/oxpsmymodulepageTest.php',
                'tests/unit/modules/models/oxpsmymoduleitemTest.php',
            ),
            array(
                'tests/unit/modules/models/oxpsmymoduleoxarticleTest.php' => 'oxArticle',
                'tests/unit/modules/controllers/oxpsmymodulepageTest.php' => 'Page',
                'tests/unit/modules/models/oxpsmymoduleitemTest.php'      => 'Item',
            )
        );

        // File system helper mock
        $oFileSystem = $this->getMock(
            'oxpsModuleGeneratorFileSystem',
            array('__call', 'isFile', 'isDir', 'createFolder', 'copyFile', 'createFile', 'renameFile')
        );
        $oFileSystem->expects($this->at(0))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(1))->method('isFile')
            ->with('/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl')
            ->will($this->returnValue(true));
        $oFileSystem->expects($this->at(2))->method('isDir')
            ->with('/path/to/modules/oxps/mymodule/tests/unit/modules/')
            ->will($this->returnValue(true));

        /* Extended oxArticle model */
        $oFileSystem->expects($this->at(3))->method('createFolder')->with(
            '/path/to/modules/oxps/mymodule/tests/unit/modules/models/'
        );
        $oFileSystem->expects($this->at(4))->method('copyFile')->with(
            '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl',
            '/path/to/modules/oxps/mymodule/tests/unit/modules/models/oxpsmymoduleoxarticleTest.php'
        );

        /* New "Page" controller */
        $oFileSystem->expects($this->at(5))->method('createFolder')->with(
            '/path/to/modules/oxps/mymodule/tests/unit/modules/controllers/'
        );
        $oFileSystem->expects($this->at(6))->method('copyFile')->with(
            '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl',
            '/path/to/modules/oxps/mymodule/tests/unit/modules/controllers/oxpsmymodulepageTest.php'
        );

        /* New "Item" model */
        $oFileSystem->expects($this->at(7))->method('createFolder')->with(
            '/path/to/modules/oxps/mymodule/tests/unit/modules/models/'
        );
        $oFileSystem->expects($this->at(8))->method('copyFile')->with(
            '/path/to/modules/oxps/ModuleGenerator/Core/module.tpl/oxpstestclass.php.tpl',
            '/path/to/modules/oxps/mymodule/tests/unit/modules/models/oxpsmymoduleitemTest.php'
        );

        oxRegistry::set('oxpsModuleGeneratorFileSystem', $oFileSystem);

        // Module instance mock
        $oModule = $this->getMock(
            'oxpsModuleGeneratorOxModule',
            array('__construct', '__call', 'getFullPath', 'getModuleId')
        );
        $oModule->expects($this->any())->method('getFullPath')->will($this->returnValue('/path/to/modules/oxps/mymodule/'));
        $oModule->expects($this->any())->method('getModuleId')->will($this->returnValue('oxpsmymodule'));

        $this->SUT->fillTestsFolder(
            $oRenderHelper,
            '/path/to/modules/oxps/ModuleGenerator/',
            array('models/oxpsmymoduleoxarticle.php' => 'oxArticle'),
            array(
                'controllers/oxpsmymodulepage.php' => 'Page',
                'models/oxpsmymoduleitem.php'      => 'Item',
            )
        );
    }
}