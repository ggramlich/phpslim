// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static slim.TestSuite.getTestIncludePath;
import static util.ListUtility.list;

import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;

import fitnesse.slim.ListExecutor;
import fitnesse.slim.ListExecutorTestBase;
import fitnesse.slim.SlimFactory;

public class PhpListExecutorTest extends ListExecutorTestBase {

  private static SlimFactory slimFactory;
  
  @BeforeClass
  public static void setUpClass() {
    slimFactory = new PhpSlimFactory(getTestIncludePath());
  }

  @AfterClass
  public static void tearDownClass() {
    slimFactory.stop();
  }

  @Override
  protected ListExecutor getListExecutor() throws Exception {
    return slimFactory.getListExecutor(false);
  }

  @Override
  protected String getTestClassPath() {
    return "testModule";
  }

  @Override
  @Test
  public void callToVoidFunctionReturnsVoidValue() throws Exception {
    statements.add(list("id", "call", "testSlim", "voidFunction"));
    // There is no difference in PHP between functions returning null and void.
    respondsWith(list(list("id", null)));
  }
}
