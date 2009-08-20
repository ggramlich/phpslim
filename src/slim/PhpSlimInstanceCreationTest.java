// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.assertTrue;
import static slim.TestSuite.getTestIncludePath;

import java.lang.reflect.Proxy;

import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;

import fitnesse.slim.SlimFactory;
import fitnesse.slim.SlimInstanceCreationTestBase;

public class PhpSlimInstanceCreationTest extends SlimInstanceCreationTestBase {

  private static SlimFactory slimFactory;
  
  @BeforeClass
  public static void setUpClass() {
    // Creates Bridge only once
    slimFactory = new PhpSlimFactory(getTestIncludePath());
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }
  
  @Before
  public void setUp() throws Exception {
    caller = slimFactory.getStatementExecutor();
  }

  @Override
  protected void assertInstanceOfTestSlim(Object x) {
    assertTrue(x instanceof Proxy);
  }

  @Override
  protected String getTestClassPath() {
    return "TestModule";
  }

}
